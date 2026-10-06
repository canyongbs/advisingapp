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

use App\Listeners\RecordWorkerCountSample;
use App\Support\QueueAutoscale\WorkerCountHistory;
use Cbox\LaravelQueueAutoscale\Events\ClusterSummaryPublished;
use Cbox\LaravelQueueAutoscale\Events\ScalingDecisionMade;
use Cbox\LaravelQueueAutoscale\Scaling\ScalingDecision;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    config(['cache.stores.landlord' => ['driver' => 'array']]);
    Cache::purge('landlord');
});

function scalingDecisionFor(string $queue): ScalingDecisionMade
{
    return new ScalingDecisionMade(new ScalingDecision(
        connection: 'sqs',
        queue: $queue,
        currentWorkers: 4,
        targetWorkers: 6,
        reason: 'scale_up',
    ));
}

/**
 * @param  list<array<string, mixed>>  $workloads
 */
function clusterSummaryWith(array $workloads): ClusterSummaryPublished
{
    return new ClusterSummaryPublished(
        clusterId: 'test-cluster',
        leaderId: 'leader-1',
        summary: ['workloads' => $workloads],
        publishedAt: now()->getTimestamp() * 1000,
    );
}

it("records the autoscaler's provisioned worker count from a scaling decision", function () {
    app(RecordWorkerCountSample::class)->recordScalingDecision(scalingDecisionFor('default'));

    expect(app(WorkerCountHistory::class)->series('default'))->toHaveCount(1)
        ->and(app(WorkerCountHistory::class)->latest('default'))->toBe(4);
});

it('records at most one sample per queue within the throttle window', function () {
    $listener = app(RecordWorkerCountSample::class);

    $listener->recordScalingDecision(scalingDecisionFor('default'));
    $listener->recordScalingDecision(scalingDecisionFor('default'));

    expect(app(WorkerCountHistory::class)->series('default'))->toHaveCount(1);
});

it('records the cluster-wide worker count for each workload from the cluster summary', function () {
    app(RecordWorkerCountSample::class)->recordClusterSummary(clusterSummaryWith([
        ['name' => 'outbound-communication', 'current_workers' => 7],
        ['name' => 'default', 'current_workers' => 2],
    ]));

    expect(app(WorkerCountHistory::class)->latest('outbound-communication'))->toBe(7)
        ->and(app(WorkerCountHistory::class)->latest('default'))->toBe(2);
});

it('ignores cluster summary workloads missing a name or count', function () {
    app(RecordWorkerCountSample::class)->recordClusterSummary(clusterSummaryWith([
        ['name' => 'outbound-communication', 'current_workers' => 5],
        ['current_workers' => 9],
        ['name' => 'default'],
    ]));

    expect(app(WorkerCountHistory::class)->latest('outbound-communication'))->toBe(5)
        ->and(app(WorkerCountHistory::class)->series('default'))->toBeEmpty();
});
