<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Support\Ecs;

use App\Enums\WorkerDrainReason;
use App\Exceptions\WorkerDrainOverdueException;
use App\Support\QueueAutoscale\QueueAutoscaleService;
use Aws\Exception\AwsException;
use Carbon\CarbonImmutable;
use Cbox\LaravelQueueAutoscale\Configuration\AutoscaleConfiguration;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Drains this worker task when ECS wants it gone: its deployment is no longer the service's primary one, or scale-in
 * wants fewer tasks while every task is protected. Once no worker is left, it releases the task's scale-in protection
 * so ECS stops it. A scale-in drain that is no longer needed is cancelled and the task takes jobs again.
 *
 * Any failure leaves the task as it is: protected, and never cutting off a job.
 */
class WorkerDrainCoordinator
{
    private const int EXCESS_CONFIRMATION_SECONDS = 30;

    private ?EcsTaskIdentity $task = null;

    private ?CarbonImmutable $excessSince = null;

    private ?string $lastFailure = null;

    private bool $hasReportedOverdueDrain = false;

    public function __construct(
        private EcsWorkerServiceClient $ecs,
        private WorkerDrainState $state,
        private QueueAutoscaleService $queueAutoscale,
        private EcsTaskProtector $protector,
        private ScaleInDrainClaims $claims,
    ) {}

    public function check(): void
    {
        $exception = $this->attempt($this->evaluate(...));

        if ($this->state->isDraining()) {
            $exception ??= $this->attempt($this->finishDrain(...));
            $this->reportOverdueDrain();
        }

        $this->noteOutcome($exception);
    }

    public function isDraining(): bool
    {
        return $this->state->isDraining();
    }

    public function drainManually(): void
    {
        $this->drain(WorkerDrainReason::Manual);
    }

    /**
     * Returns false for a drain that cannot be cancelled: a deployment drain, or one that has released protection, so
     * ECS may already be stopping the task.
     */
    public function resume(): bool
    {
        $reason = $this->state->reason();

        if ($reason === null) {
            return true;
        }

        if (($reason === WorkerDrainReason::Deployment) || $this->state->isProtectionReleased()) {
            return false;
        }

        $this->queueAutoscale->start();
        $this->state->clear();

        $this->log()->info('Worker task drain cancelled; taking jobs again', ['reason' => $reason->value]);

        return true;
    }

    /**
     * @param callable(): void $step
     */
    private function attempt(callable $step): ?Throwable
    {
        try {
            $step();

            return null;
        } catch (Throwable $exception) {
            return $exception;
        }
    }

    private function noteOutcome(?Throwable $exception): void
    {
        if ($exception === null) {
            if ($this->lastFailure !== null) {
                $this->log()->info('Worker drain checks are working again');

                $this->lastFailure = null;
            }

            return;
        }

        $failure = $exception::class . ':' . (($exception instanceof AwsException) ? $exception->getAwsErrorCode() : '');

        // The same failure would otherwise be logged on every check until it is fixed.
        if ($failure !== $this->lastFailure) {
            $this->log()->warning('Worker drain check failed; leaving the task as it is', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }

        $this->lastFailure = $failure;
    }

    private function evaluate(): void
    {
        $reason = $this->state->reason();

        // Only a scale-in drain can become unnecessary; any other drain runs to the end.
        if (in_array($reason, [WorkerDrainReason::Deployment, WorkerDrainReason::Manual], strict: true) || $this->state->isAwaitingReplacement()) {
            return;
        }

        $isReleased = $this->state->isProtectionReleased();

        $task = $this->task ??= $this->ecs->identifyCurrentTask();

        if ($task === null) {
            return;
        }

        $service = $this->ecs->describeService($task);

        if ($service->primaryDeploymentId !== $task->startedBy) {
            // A task that has released protection is already free for the deployment to stop.
            if (! $isReleased) {
                $this->drain(WorkerDrainReason::Deployment, [
                    'started_by' => $task->startedBy,
                    'primary_deployment' => $service->primaryDeploymentId,
                ]);
            }

            return;
        }

        if ($service->deploymentCount !== 1) {
            $this->excessSince = null;

            return;
        }

        $isClaimed = $reason === WorkerDrainReason::ScaleIn;

        // Listing tasks costs another rate-limited API call, so only do it when scale-in could need this task.
        $runningTaskArns = ($isClaimed || ($service->runningCount > $service->desiredCount))
            ? $this->ecs->runningTaskArns($task)
            : null;

        // ECS is already stopping this task, so changing course now could start a job it would cut off.
        if (($runningTaskArns !== null) && (! in_array($task->taskArn, $runningTaskArns, strict: true))) {
            return;
        }

        $excess = ($runningTaskArns === null) ? 0 : (count($runningTaskArns) - $service->desiredCount);

        // ECS stops unprotected tasks itself, and runs an extra task while it replaces an unhealthy one, so neither needs a drain.
        if (($runningTaskArns !== null) && ($excess > 0)) {
            $excess -= count($this->ecs->stoppableTaskArns($task, array_values(array_diff($runningTaskArns, [$task->taskArn]))));
        }

        if ($excess > 0) {
            $this->excessSince ??= CarbonImmutable::now();
        } else {
            $this->excessSince = null;
        }

        // ECS needs a moment to pick an unprotected task to stop, so only excess that persists means every task is protected.
        $isExcessConfirmed = $this->excessSince?->addSeconds(self::EXCESS_CONFIRMATION_SECONDS)->isPast() ?? false;

        $shouldDrain = $this->claims->reconcile(
            $task,
            $this->protector->protectedUntil(),
            $runningTaskArns,
            ($isClaimed || $isExcessConfirmed) ? $excess : 0,
            $isClaimed,
        );

        if ($shouldDrain && (! $isClaimed)) {
            $this->drain(WorkerDrainReason::ScaleIn, [
                'desired_count' => $service->desiredCount,
                'running_count' => $runningTaskArns === null ? $service->runningCount : count($runningTaskArns),
            ]);
        } elseif ((! $shouldDrain) && $isClaimed) {
            if ($isReleased) {
                $this->replaceIdleTask();
            } else {
                $this->resume();
            }
        }
    }

    /**
     * ECS may already be stopping a task that released protection, so instead of taking jobs again it is replaced.
     */
    private function replaceIdleTask(): void
    {
        $this->state->markForReplacement();

        $this->log()->info('Drained worker task is no longer needed; failing its healthcheck so ECS replaces it');
    }

    /**
     * @param array<string, mixed> $context
     */
    private function drain(WorkerDrainReason $reason, array $context = []): void
    {
        $this->state->start($reason);
        $this->hasReportedOverdueDrain = false;

        try {
            $this->queueAutoscale->stop();
        } catch (Throwable $exception) {
            $this->state->clear();

            throw $exception;
        }

        $this->log()->info('Worker task draining: taking no new jobs and finishing the ones it has', [
            'reason' => $reason->value,
            ...$context,
        ]);
    }

    private function finishDrain(): void
    {
        if ($this->state->isProtectionReleased()) {
            return;
        }

        // The manager is still waiting for its workers to finish their jobs.
        if ($this->queueAutoscale->isRunning()) {
            return;
        }

        // Workers that outlived their manager would otherwise keep taking jobs.
        if ($this->queueAutoscale->workerProcessIds() !== []) {
            $this->queueAutoscale->terminateWorkers();

            return;
        }

        if (! $this->protector->release()) {
            return;
        }

        $this->state->markProtectionReleased();

        $this->log()->info('Worker task drained and released its scale-in protection', [
            'reason' => $this->state->reason()?->value,
        ]);
    }

    private function reportOverdueDrain(): void
    {
        if ($this->hasReportedOverdueDrain || $this->state->isAwaitingReplacement() || (! $this->state->isOverdue())) {
            return;
        }

        $this->hasReportedOverdueDrain = true;

        $exception = WorkerDrainOverdueException::make(
            $this->state->reason(),
            WorkerDrainState::MAX_DRAIN_MINUTES,
            $this->state->isProtectionReleased(),
        );

        $this->log()->error($exception->getMessage());

        report($exception);
    }

    private function log(): LoggerInterface
    {
        return Log::channel(AutoscaleConfiguration::logChannel());
    }
}
