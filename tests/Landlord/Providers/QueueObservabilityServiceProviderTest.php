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

use App\Features\QueueMonitoringFeature;
use App\Models\Tenant;
use App\Models\User;
use App\Overrides\QueueAutoscale\ExitReportingWorkerSpawner;
use App\Providers\QueueObservabilityServiceProvider;
use App\Support\QueueAutoscale\WorkerCountHistory;
use Cbox\LaravelQueueAutoscale\Events\ClusterSummaryPublished;
use Cbox\LaravelQueueAutoscale\Events\ScalingDecisionMade;
use Cbox\LaravelQueueAutoscale\Scaling\ScalingDecision;
use Cbox\LaravelQueueAutoscale\Workers\WorkerSpawner;
use Cbox\LaravelQueueMetrics\LaravelQueueMetrics;
use Cbox\LaravelQueueMonitor\LaravelQueueMonitor;
use Cbox\LaravelQueueMonitor\Models\JobMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

function bootQueueObservabilityServiceProvider(): void
{
    $provider = app()->getProvider(QueueObservabilityServiceProvider::class);

    assert($provider instanceof QueueObservabilityServiceProvider);

    $provider->boot();
}

function dispatchWorkerCountEvents(): void
{
    event(new ScalingDecisionMade(new ScalingDecision(
        connection: 'sqs',
        queue: 'host-share',
        currentWorkers: 2,
        targetWorkers: 2,
        reason: 'hold',
    )));

    event(new ClusterSummaryPublished(
        clusterId: 'test-cluster',
        leaderId: 'leader-1',
        summary: ['workloads' => [['name' => 'cluster-wide', 'current_workers' => 8]]],
        publishedAt: now()->getTimestamp() * 1000,
    ));
}

function requestFrom(?User $user): Request
{
    $request = Request::create('/');
    $request->setUserResolver(fn (): ?User => $user);

    return $request;
}

function userWithSuperAdmin(bool $isSuperAdmin): User
{
    $user = Mockery::mock(User::class);
    $user->shouldReceive('isSuperAdmin')->andReturn($isSuperAdmin);

    assert($user instanceof User);

    return $user;
}

it('records the current tenant on monitored jobs', function () {
    $tenant = Tenant::query()->first();

    $jobMonitor = $tenant->execute(fn (): JobMonitor => JobMonitor::factory()->create());

    expect($jobMonitor->refresh()->getAttribute('tenant_id'))->toBe($tenant->getKey());
});

it('records no tenant on jobs monitored outside a tenant context', function () {
    Tenant::forgetCurrent();

    $jobMonitor = JobMonitor::factory()->create();

    expect($jobMonitor->refresh()->getAttribute('tenant_id'))->toBeNull();
});

it('disables the queue monitor while `QueueMonitoringFeature` is inactive', function () {
    QueueMonitoringFeature::deactivate();

    config(['queue-monitor.enabled' => true]);

    $provider = app()->getProvider(QueueObservabilityServiceProvider::class);

    assert($provider instanceof QueueObservabilityServiceProvider);

    $provider->boot();

    expect(config('queue-monitor.enabled'))->toBeFalse();
});

it('leaves the queue monitor enabled once `QueueMonitoringFeature` is active', function () {
    config(['queue-monitor.enabled' => true]);

    $provider = app()->getProvider(QueueObservabilityServiceProvider::class);

    assert($provider instanceof QueueObservabilityServiceProvider);

    $provider->boot();

    expect(config('queue-monitor.enabled'))->toBeTrue();
});

it('replaces the autoscale worker spawner with one whose workers report how they exit', function () {
    expect(app(WorkerSpawner::class))->toBeInstanceOf(ExitReportingWorkerSpawner::class);
});

describe('worker count sampling', function () {
    beforeEach(function () {
        config(['cache.stores.landlord' => ['driver' => 'array']]);
        Cache::purge('landlord');

        Event::forget(ScalingDecisionMade::class);
        Event::forget(ClusterSummaryPublished::class);
    });

    it('samples the cluster-wide count from the cluster summary in cluster mode', function () {
        config(['queue-autoscale.cluster.enabled' => true]);

        bootQueueObservabilityServiceProvider();
        dispatchWorkerCountEvents();

        expect(app(WorkerCountHistory::class)->queues())->toBe(['cluster-wide']);
    });

    it('samples scaling decisions in single-host mode', function () {
        config(['queue-autoscale.cluster.enabled' => false]);

        bootQueueObservabilityServiceProvider();
        dispatchWorkerCountEvents();

        expect(app(WorkerCountHistory::class)->queues())->toBe(['host-share']);
    });
});

it('only lets super admins through the queue package endpoints', function (Closure $user, bool $allowed) {
    $request = requestFrom($user());

    expect(LaravelQueueMonitor::check($request))->toBe($allowed)
        ->and(app(LaravelQueueMetrics::class)->check($request))->toBe($allowed);
})->with([
    'a super admin' => [fn (): User => userWithSuperAdmin(true), true],
    'any other user' => [fn (): User => userWithSuperAdmin(false), false],
    'a guest' => [fn (): ?User => null, false],
]);
