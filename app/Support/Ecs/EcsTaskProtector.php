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

use App\Exceptions\EcsTaskProtectionException;
use Carbon\CarbonImmutable;
use Cbox\LaravelQueueAutoscale\Configuration\AutoscaleConfiguration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Acquires ECS task scale-in protection for the duration of a long-running job, so scale-in and rolling deployments
 * do not stop the task mid-job.
 *
 * Jobs never disable protection themselves: several worker processes share one task, so a per-job release would let
 * one worker strip a sibling's protection. It is released only once the task has drained, when no worker is left.
 * A task-local file coalesces requests so the rate-limited UpdateTaskProtection API is not called on every job.
 */
class EcsTaskProtector
{
    public const string STATE_FILENAME = 'advisingapp-ecs-task-protection';

    public const string LOCK_FILENAME = 'advisingapp-ecs-task-protection.lock';

    private const int REFRESH_BUFFER_SECONDS = 60;

    private const int HTTP_TIMEOUT_SECONDS = 5;

    private const int EXPIRY_HEADROOM_MINUTES = 5;

    private const string DEPLOYMENT_BLOCKED = 'DEPLOYMENT_BLOCKED';

    public function __construct(
        private ?string $stateDirectory = null,
    ) {}

    public function protectForJobTimeout(int $jobTimeoutSeconds): void
    {
        $agentUri = $this->agentUri();

        if ($agentUri === null) {
            return;
        }

        // Every request resets the expiry, so checking stale state outside the lock could replace a longer window.
        $this->whileLocked(function () use ($agentUri, $jobTimeoutSeconds): void {
            if (! $this->needsRefresh($jobTimeoutSeconds)) {
                return;
            }

            $expiresInMinutes = (int) ceil($jobTimeoutSeconds / 60) + self::EXPIRY_HEADROOM_MINUTES;

            if ($this->request($agentUri, ['ProtectionEnabled' => true, 'ExpiresInMinutes' => $expiresInMinutes])) {
                $this->rememberProtectedUntil(CarbonImmutable::now()->addMinutes($expiresInMinutes));
            }
        });
    }

    /**
     * Only call once no worker on this task is running a job.
     */
    public function release(): bool
    {
        $agentUri = $this->agentUri();

        return $this->whileLocked(function () use ($agentUri): bool {
            if (($agentUri !== null) && (! $this->request($agentUri, ['ProtectionEnabled' => false]))) {
                return false;
            }

            @unlink($this->path(self::STATE_FILENAME));

            return true;
        });
    }

    public function protectedUntil(): ?CarbonImmutable
    {
        return $this->readProtectedUntil();
    }

    private function agentUri(): ?string
    {
        $uri = Str::rtrim(Config::string('app.ecs_agent_uri'), '/');

        return blank($uri) ? null : $uri;
    }

    private function needsRefresh(int $jobTimeoutSeconds): bool
    {
        $protectedUntil = $this->readProtectedUntil();

        return $protectedUntil === null
            || $protectedUntil->subSeconds(self::REFRESH_BUFFER_SECONDS)->isBefore(CarbonImmutable::now()->addSeconds($jobTimeoutSeconds));
    }

    /**
     * @param array<string, bool|int> $state
     */
    private function request(string $agentUri, array $state): bool
    {
        try {
            $response = Http::timeout(self::HTTP_TIMEOUT_SECONDS)
                ->acceptJson()
                ->put("{$agentUri}/task-protection/v1/state", $state);

            // The agent can answer 200 with a `failure` (ECS refused the change) or `error` (calling ECS failed) body.
            if ($response->successful() && ($response->json('failure') === null) && ($response->json('error') === null)) {
                return true;
            }

            // ECS refuses new protection while it is waiting to stop a task and every task is protected; the drain watcher resolves that.
            if ($response->json('failure.Reason') === self::DEPLOYMENT_BLOCKED) {
                Log::channel(AutoscaleConfiguration::logChannel())->info('Task protection not extended: ECS is waiting to stop a protected task');

                return false;
            }

            $reason = collect([
                $response->json('failure.Reason'),
                $response->json('failure.Detail'),
                $response->json('error.Code'),
                $response->json('error.Message'),
            ])
                ->filter(fn (mixed $value): bool => is_string($value) && filled($value))
                ->implode(' ');

            report(EcsTaskProtectionException::requestRejected($response->status(), $reason));
        } catch (Throwable $exception) {
            report(EcsTaskProtectionException::requestFailed($exception));
        }

        return false;
    }

    /**
     * @template TReturn
     *
     * @param callable(): TReturn $callback
     *
     * @return TReturn
     */
    private function whileLocked(callable $callback): mixed
    {
        $handle = @fopen($this->path(self::LOCK_FILENAME), 'c');

        if ($handle === false) {
            return $callback();
        }

        try {
            flock($handle, LOCK_EX);

            return $callback();
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function path(string $filename): string
    {
        return ($this->stateDirectory ?? sys_get_temp_dir()) . '/' . $filename;
    }

    private function readProtectedUntil(): ?CarbonImmutable
    {
        $path = $this->path(self::STATE_FILENAME);

        if (! is_file($path)) {
            return null;
        }

        $contents = @file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $timestamp = Str::trim($contents);

        if (! ctype_digit($timestamp)) {
            return null;
        }

        return CarbonImmutable::createFromTimestamp((int) $timestamp);
    }

    private function rememberProtectedUntil(CarbonImmutable $until): void
    {
        @file_put_contents($this->path(self::STATE_FILENAME), (string) $until->getTimestamp(), LOCK_EX);
    }
}
