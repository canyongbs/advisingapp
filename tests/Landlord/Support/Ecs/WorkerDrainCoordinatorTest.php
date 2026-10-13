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

use App\Enums\WorkerDrainReason;
use App\Exceptions\WorkerDrainOverdueException;
use App\Support\Ecs\EcsServiceState;
use App\Support\Ecs\EcsTaskIdentity;
use App\Support\Ecs\EcsTaskProtector;
use App\Support\Ecs\EcsWorkerServiceClient;
use App\Support\Ecs\ScaleInDrainClaims;
use App\Support\Ecs\WorkerDrainCoordinator;
use App\Support\Ecs\WorkerDrainState;
use App\Support\QueueAutoscale\QueueAutoscaleService;
use Aws\Command;
use Aws\Exception\AwsException;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\File;
use Mockery\MockInterface;

use function Pest\Laravel\travel;

// Per process, so parallel test workers do not share drain state.
$directory = sys_get_temp_dir() . '/worker-drain-coordinator-' . getmypid();

$task = new EcsTaskIdentity('task-a', 'cluster-arn', 'worker-service', 'ecs-svc/current');

beforeEach(function () use ($directory) {
    config(['queue-autoscale.manager.log_channel' => 'null']);
    Event::fake([MessageLogged::class]);

    File::deleteDirectory($directory);
    File::ensureDirectoryExists($directory);
});

afterEach(function () use ($directory) {
    File::deleteDirectory($directory);
});

/**
 * @return array{WorkerDrainCoordinator, WorkerDrainState, EcsWorkerServiceClient&MockInterface, QueueAutoscaleService&MockInterface, EcsTaskProtector&MockInterface, ScaleInDrainClaims&MockInterface}
 */
$drainFixture = function () use ($directory, $task): array {
    $ecs = Mockery::mock(EcsWorkerServiceClient::class);
    $ecs->allows('identifyCurrentTask')->andReturn($task)->byDefault();

    $queueAutoscale = Mockery::mock(QueueAutoscaleService::class);

    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->allows('protectedUntil')->andReturnNull()->byDefault();

    $claims = Mockery::mock(ScaleInDrainClaims::class);
    $state = new WorkerDrainState($directory);

    assert($ecs instanceof EcsWorkerServiceClient);
    assert($queueAutoscale instanceof QueueAutoscaleService);
    assert($protector instanceof EcsTaskProtector);
    assert($claims instanceof ScaleInDrainClaims);

    return [new WorkerDrainCoordinator($ecs, $state, $queueAutoscale, $protector, $claims), $state, $ecs, $queueAutoscale, $protector, $claims];
};

$serviceState = fn (string $primaryDeploymentId = 'ecs-svc/current', int $deploymentCount = 1, int $desiredCount = 2, int $runningCount = 2): EcsServiceState => new EcsServiceState(
    primaryDeploymentId: $primaryDeploymentId,
    deploymentCount: $deploymentCount,
    desiredCount: $desiredCount,
    runningCount: $runningCount,
);

$wasLogged = fn (string $message): bool => Event::dispatched(MessageLogged::class)
    ->contains(fn (array $arguments): bool => $arguments[0]->message === $message);

describe('deployment', function () use ($drainFixture, $serviceState, $wasLogged, $task) {
    it('drains the task once its deployment is no longer the primary one', function () use ($drainFixture, $serviceState, $wasLogged) {
        [$coordinator, $state, $ecs, $queueAutoscale] = $drainFixture();
        $ecs->allows('describeService')->andReturn($serviceState(primaryDeploymentId: 'ecs-svc/newer', deploymentCount: 2));
        $queueAutoscale->expects('stop')->once();
        $queueAutoscale->allows('isRunning')->andReturnTrue();

        expect($state->isDraining())->toBeFalse();

        $coordinator->check();

        expect($state->reason())->toBe(WorkerDrainReason::Deployment)
            ->and($wasLogged('Worker task draining: taking no new jobs and finishing the ones it has'))->toBeTrue();
    });

    it('does not drain a task started by the primary deployment', function () use ($drainFixture, $serviceState, $task) {
        [$coordinator, $state, $ecs, $queueAutoscale, , $claims] = $drainFixture();
        $ecs->allows('describeService')->andReturn($serviceState());
        $ecs->expects('runningTaskArns')->never();
        $claims->expects('reconcile')->with($task, null, null, 0, false)->andReturnFalse();
        $queueAutoscale->expects('stop')->never();

        $coordinator->check();

        expect($state->isDraining())->toBeFalse();
    });

    it('never cancels a deployment drain', function () use ($drainFixture) {
        [$coordinator, $state, $ecs, $queueAutoscale] = $drainFixture();
        $state->start(WorkerDrainReason::Deployment);
        $ecs->expects('describeService')->never();
        $queueAutoscale->expects('start')->never();
        $queueAutoscale->allows('isRunning')->andReturnTrue();

        $coordinator->check();

        expect($state->reason())->toBe(WorkerDrainReason::Deployment);
    });

    it('refuses to cancel a deployment drain by hand', function () use ($drainFixture) {
        [$coordinator, $state, , $queueAutoscale] = $drainFixture();
        $state->start(WorkerDrainReason::Deployment);
        $queueAutoscale->expects('start')->never();

        expect($coordinator->resume())->toBeFalse()
            ->and($state->reason())->toBe(WorkerDrainReason::Deployment);
    });
});

describe('scale-in', function () use ($drainFixture, $serviceState, $wasLogged, $task) {
    it('only counts excess tasks that are still there 30 seconds later', function () use ($drainFixture, $serviceState, $task) {
        [$coordinator, , $ecs, , , $claims] = $drainFixture();
        $ecs->allows('describeService')->andReturn($serviceState(desiredCount: 1));
        $ecs->allows('runningTaskArns')->andReturn(['task-a', 'task-b']);

        $claims->expects('reconcile')->twice()->with($task, null, ['task-a', 'task-b'], 0, false)->andReturnFalse()->ordered();
        $claims->expects('reconcile')->with($task, null, ['task-a', 'task-b'], 1, false)->andReturnFalse()->ordered();

        $coordinator->check();

        travel(20)->seconds();

        $coordinator->check();

        travel(11)->seconds();

        $coordinator->check();
    });

    it('starts timing excess tasks again once they are gone', function () use ($drainFixture, $serviceState, $task) {
        [$coordinator, , $ecs, , , $claims] = $drainFixture();
        $ecs->allows('describeService')->andReturn($serviceState(desiredCount: 1), $serviceState(), $serviceState(desiredCount: 1));
        $ecs->allows('runningTaskArns')->andReturn(['task-a', 'task-b']);
        $claims->expects('reconcile')->times(3)->with($task, null, Mockery::any(), 0, false)->andReturnFalse();

        $coordinator->check();

        travel(31)->seconds();

        $coordinator->check();
        $coordinator->check();
    });

    it('drains the task when it is chosen to leave', function () use ($drainFixture, $serviceState) {
        [$coordinator, $state, $ecs, $queueAutoscale, , $claims] = $drainFixture();
        $ecs->allows('describeService')->andReturn($serviceState(desiredCount: 1));
        $ecs->allows('runningTaskArns')->andReturn(['task-a', 'task-b']);
        $claims->allows('reconcile')->andReturnTrue();
        $queueAutoscale->expects('stop')->once();
        $queueAutoscale->allows('isRunning')->andReturnTrue();

        $coordinator->check();

        expect($state->reason())->toBe(WorkerDrainReason::ScaleIn);
    });

    it('takes jobs again when scale-in no longer needs the task', function () use ($drainFixture, $serviceState, $wasLogged, $task) {
        [$coordinator, $state, $ecs, $queueAutoscale, , $claims] = $drainFixture();
        $state->start(WorkerDrainReason::ScaleIn);
        $ecs->allows('describeService')->andReturn($serviceState());
        $ecs->allows('runningTaskArns')->andReturn(['task-a', 'task-b']);
        $claims->expects('reconcile')->with($task, null, ['task-a', 'task-b'], 0, true)->andReturnFalse();
        $queueAutoscale->expects('start')->once();

        $coordinator->check();

        expect($state->isDraining())->toBeFalse()
            ->and($wasLogged('Worker task drain cancelled; taking jobs again'))->toBeTrue();
    });

    it('leaves the drain alone once ECS is stopping the task', function () use ($drainFixture, $serviceState) {
        [$coordinator, $state, $ecs, $queueAutoscale, , $claims] = $drainFixture();
        $state->start(WorkerDrainReason::ScaleIn);
        $ecs->allows('describeService')->andReturn($serviceState(desiredCount: 1));
        $ecs->allows('runningTaskArns')->andReturn(['task-b']);
        $claims->expects('reconcile')->never();
        $queueAutoscale->expects('start')->never();
        $queueAutoscale->allows('isRunning')->andReturnTrue();

        $coordinator->check();

        expect($state->reason())->toBe(WorkerDrainReason::ScaleIn);
    });

    it('keeps its claim after releasing protection until ECS stops the task', function () use ($drainFixture, $serviceState, $task) {
        [$coordinator, $state, $ecs, $queueAutoscale, , $claims] = $drainFixture();
        $state->start(WorkerDrainReason::ScaleIn);
        $state->markProtectionReleased();
        $ecs->allows('describeService')->andReturn($serviceState(desiredCount: 1));
        $ecs->allows('runningTaskArns')->andReturn(['task-a', 'task-b']);
        $claims->expects('reconcile')->with($task, null, ['task-a', 'task-b'], 1, true)->andReturnTrue();
        $queueAutoscale->expects('start')->never();

        $coordinator->check();

        expect($state->reason())->toBe(WorkerDrainReason::ScaleIn)
            ->and($state->isProtectionReleased())->toBeTrue()
            ->and($state->isAwaitingReplacement())->toBeFalse();
    });

    it('has ECS replace the task instead of taking jobs again when it is no longer needed after releasing protection', function () use ($drainFixture, $serviceState, $wasLogged) {
        Exceptions::fake();

        [$coordinator, $state, $ecs, $queueAutoscale, , $claims] = $drainFixture();
        $state->start(WorkerDrainReason::ScaleIn);
        $state->markProtectionReleased();
        $ecs->expects('describeService')->once()->andReturn($serviceState());
        $ecs->allows('runningTaskArns')->andReturn(['task-a', 'task-b']);
        $claims->expects('reconcile')->once()->andReturnFalse();
        $queueAutoscale->expects('start')->never();

        $coordinator->check();
        $coordinator->check();

        expect($state->isAwaitingReplacement())->toBeTrue()
            ->and($state->isOverdue())->toBeTrue()
            ->and($wasLogged('Drained worker task is no longer needed; failing its healthcheck so ECS replaces it'))->toBeTrue();

        Exceptions::assertNothingReported();
    });

    it('leaves a task that released protection for a deployment to stop', function () use ($drainFixture, $serviceState) {
        [$coordinator, $state, $ecs, $queueAutoscale] = $drainFixture();
        $state->start(WorkerDrainReason::ScaleIn);
        $state->markProtectionReleased();
        $ecs->allows('describeService')->andReturn($serviceState(primaryDeploymentId: 'ecs-svc/newer', deploymentCount: 2));
        $queueAutoscale->expects('stop')->never();

        $coordinator->check();

        expect($state->reason())->toBe(WorkerDrainReason::ScaleIn)
            ->and($state->isProtectionReleased())->toBeTrue();
    });

    it('does not consider scale-in while a deployment is in progress', function () use ($drainFixture, $serviceState) {
        [$coordinator, $state, $ecs, , , $claims] = $drainFixture();
        $ecs->allows('describeService')->andReturn($serviceState(deploymentCount: 2, desiredCount: 1));
        $ecs->expects('runningTaskArns')->never();
        $claims->expects('reconcile')->never();

        $coordinator->check();

        expect($state->isDraining())->toBeFalse();
    });
});

describe('failures', function () use ($drainFixture, $serviceState, $wasLogged) {
    it('leaves the task as it is when ECS cannot be read', function () use ($drainFixture, $wasLogged) {
        [$coordinator, $state, $ecs, $queueAutoscale] = $drainFixture();
        $ecs->allows('describeService')->andThrow(new AwsException('AccessDenied', new Command('DescribeServices')));
        $queueAutoscale->expects('stop')->never();

        $coordinator->check();

        expect($state->isDraining())->toBeFalse()
            ->and($wasLogged('Worker drain check failed; leaving the task as it is'))->toBeTrue();
    });

    it('logs a failure that keeps happening once, and when checks work again', function () use ($drainFixture, $serviceState, $wasLogged) {
        [$coordinator, , $ecs, , , $claims] = $drainFixture();
        $ecs->expects('describeService')->times(3)->andThrow(new AwsException('AccessDenied', new Command('DescribeServices'), ['code' => 'AccessDeniedException']));
        $ecs->expects('describeService')->once()->andReturn($serviceState());
        $claims->allows('reconcile')->andReturnFalse();

        $coordinator->check();
        $coordinator->check();
        $coordinator->check();

        $failureLogs = Event::dispatched(MessageLogged::class)
            ->filter(fn (array $arguments): bool => $arguments[0]->message === 'Worker drain check failed; leaving the task as it is');

        expect($failureLogs)->toHaveCount(1)
            ->and($wasLogged('Worker drain checks are working again'))->toBeFalse();

        $coordinator->check();

        expect($wasLogged('Worker drain checks are working again'))->toBeTrue();
    });

    it('does not record a drain when the queue autoscaler could not be stopped', function () use ($drainFixture, $serviceState) {
        [$coordinator, $state, $ecs, $queueAutoscale] = $drainFixture();
        $ecs->allows('describeService')->andReturn($serviceState(primaryDeploymentId: 'ecs-svc/newer'));
        $queueAutoscale->expects('stop')->andThrow(new RuntimeException('s6-svc failed'));

        $coordinator->check();

        expect($state->isDraining())->toBeFalse();
    });

    it('does nothing off ECS', function () use ($drainFixture) {
        [$coordinator, $state, $ecs] = $drainFixture();
        $ecs->allows('identifyCurrentTask')->andReturnNull();
        $ecs->expects('describeService')->never();

        $coordinator->check();

        expect($state->isDraining())->toBeFalse();
    });
});

describe('releasing protection', function () use ($drainFixture, $wasLogged) {
    it('waits while the queue autoscaler is still stopping its workers', function () use ($drainFixture) {
        [$coordinator, $state, , $queueAutoscale, $protector] = $drainFixture();
        $state->start(WorkerDrainReason::Manual);
        $queueAutoscale->allows('isRunning')->andReturnTrue();
        $protector->expects('release')->never();

        $coordinator->check();

        expect($state->isProtectionReleased())->toBeFalse();
    });

    it('stops workers that outlived their manager instead of releasing protection under them', function () use ($drainFixture) {
        [$coordinator, $state, , $queueAutoscale, $protector] = $drainFixture();
        $state->start(WorkerDrainReason::Manual);
        $queueAutoscale->allows('isRunning')->andReturnFalse();
        $queueAutoscale->allows('workerProcessIds')->andReturn([123]);
        $queueAutoscale->expects('terminateWorkers')->once();
        $protector->expects('release')->never();

        $coordinator->check();

        expect($state->isProtectionReleased())->toBeFalse();
    });

    it('releases protection once no worker is left, and only once', function () use ($drainFixture, $wasLogged) {
        [$coordinator, $state, , $queueAutoscale, $protector] = $drainFixture();
        $state->start(WorkerDrainReason::Manual);
        $queueAutoscale->allows('isRunning')->andReturnFalse();
        $queueAutoscale->allows('workerProcessIds')->andReturn([]);
        $protector->expects('release')->once()->andReturnTrue();

        $coordinator->check();
        $coordinator->check();

        expect($state->isProtectionReleased())->toBeTrue()
            ->and($wasLogged('Worker task drained and released its scale-in protection'))->toBeTrue();
    });

    it('tries again when the release fails', function () use ($drainFixture) {
        [$coordinator, $state, , $queueAutoscale, $protector] = $drainFixture();
        $state->start(WorkerDrainReason::Manual);
        $queueAutoscale->allows('isRunning')->andReturnFalse();
        $queueAutoscale->allows('workerProcessIds')->andReturn([]);
        $protector->expects('release')->twice()->andReturn(false, true);

        $coordinator->check();

        expect($state->isProtectionReleased())->toBeFalse();

        $coordinator->check();

        expect($state->isProtectionReleased())->toBeTrue();
    });
});

describe('stuck drains', function () use ($drainFixture, $directory, $wasLogged) {
    it('reports a drain that has outlived the healthcheck limit, once', function () use ($drainFixture, $directory, $wasLogged) {
        Exceptions::fake();

        [$coordinator, $state, , $queueAutoscale] = $drainFixture();
        $state->start(WorkerDrainReason::ScaleIn);
        $queueAutoscale->allows('isRunning')->andReturnTrue();

        $coordinator->check();

        Exceptions::assertNothingReported();

        touch($directory . '/' . WorkerDrainState::FILENAME, now()->subMinutes(31)->getTimestamp());

        $coordinator->check();
        $coordinator->check();

        Exceptions::assertReportedCount(1);
        Exceptions::assertReported(WorkerDrainOverdueException::class);

        expect($wasLogged(WorkerDrainOverdueException::make(WorkerDrainReason::ScaleIn, 30, false)->getMessage()))->toBeTrue();
    });
});

describe('manual drain', function () use ($drainFixture) {
    it('drains on request and takes jobs again when cancelled', function () use ($drainFixture) {
        [$coordinator, $state, , $queueAutoscale] = $drainFixture();
        $queueAutoscale->expects('stop')->once();
        $queueAutoscale->expects('start')->once();

        $coordinator->drainManually();

        expect($state->reason())->toBe(WorkerDrainReason::Manual)
            ->and($coordinator->resume())->toBeTrue()
            ->and($state->isDraining())->toBeFalse();
    });

    it('refuses to cancel once protection was released', function () use ($drainFixture) {
        [$coordinator, $state, , $queueAutoscale] = $drainFixture();
        $state->start(WorkerDrainReason::Manual);
        $state->markProtectionReleased();
        $queueAutoscale->expects('start')->never();

        expect($coordinator->resume())->toBeFalse()
            ->and($state->reason())->toBe(WorkerDrainReason::Manual);
    });

    it('has nothing to cancel when not draining', function () use ($drainFixture) {
        [$coordinator, , , $queueAutoscale] = $drainFixture();
        $queueAutoscale->expects('start')->never();

        expect($coordinator->resume())->toBeTrue();
    });
});
