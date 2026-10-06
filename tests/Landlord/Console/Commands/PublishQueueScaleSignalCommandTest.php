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

use Aws\CloudWatch\CloudWatchClient;
use Cbox\LaravelQueueAutoscale\Contracts\ClusterStoreContract;
use Cbox\LaravelQueueAutoscale\LaravelQueueAutoscale;
use Mockery\MockInterface;

use function Pest\Laravel\artisan;

/**
 * @param  array<string, mixed>  $summary
 */
function fakeClusterSummary(array $summary): void
{
    config(['queue-autoscale.cluster.enabled' => true]);

    $store = Mockery::mock(ClusterStoreContract::class);
    $store->shouldReceive('summary')->andReturn($summary);

    app()->instance(ClusterStoreContract::class, $store);
    app()->forgetInstance(LaravelQueueAutoscale::class);
}

function fakeCloudWatch(): MockInterface
{
    $cloudWatch = Mockery::mock(CloudWatchClient::class);

    app()->instance(CloudWatchClient::class, $cloudWatch);

    return $cloudWatch;
}

/**
 * @param  array<string, mixed>  $arguments
 *
 * @return array<string, int>
 */
function publishedMetricValues(array $arguments): array
{
    $metricData = $arguments['MetricData'];

    assert(is_array($metricData));

    return collect($metricData)->mapWithKeys(fn (array $metric): array => [$metric['MetricName'] => $metric['Value']])->all();
}

it('publishes the required workers, capacity and deficit from the cluster summary', function () {
    fakeClusterSummary(['required_workers' => 518, 'total_worker_capacity' => 17]);

    fakeCloudWatch()->shouldReceive('putMetricData')->once()->withArgs(function (array $arguments): bool {
        return $arguments['Namespace'] === 'CanyonGBS/QueueAutoscale'
            && publishedMetricValues($arguments) === ['RequiredWorkers' => 518, 'WorkerCapacity' => 17, 'WorkerCapacityDeficit' => 501];
    });

    artisan('queue:autoscale:publish-scale-signal')->assertSuccessful();
});

it('clamps the deficit at zero when capacity covers demand', function () {
    fakeClusterSummary(['required_workers' => 20, 'total_worker_capacity' => 51]);

    fakeCloudWatch()->shouldReceive('putMetricData')->once()->withArgs(
        fn (array $arguments): bool => publishedMetricValues($arguments)['WorkerCapacityDeficit'] === 0,
    );

    artisan('queue:autoscale:publish-scale-signal')->assertSuccessful();
});

it('publishes nothing when no cluster summary is available', function () {
    fakeClusterSummary([]);

    fakeCloudWatch()->shouldNotReceive('putMetricData');

    artisan('queue:autoscale:publish-scale-signal')->assertSuccessful();
});

it('fails when the cluster summary is missing the worker counts', function () {
    fakeClusterSummary(['manager_count' => 3]);

    fakeCloudWatch()->shouldNotReceive('putMetricData');

    artisan('queue:autoscale:publish-scale-signal')->assertFailed();
});
