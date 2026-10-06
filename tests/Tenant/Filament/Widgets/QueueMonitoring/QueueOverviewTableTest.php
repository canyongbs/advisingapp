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

use App\Filament\Widgets\QueueMonitoring\QueueOverviewTable;
use App\Support\QueueAutoscale\WorkerCountHistory;
use Cbox\LaravelQueueAutoscale\Contracts\PickupTimeStoreContract;
use Cbox\LaravelQueueMetrics\DataTransferObjects\QueueDepthData;
use Cbox\LaravelQueueMetrics\DataTransferObjects\QueueMetricsData;
use Cbox\LaravelQueueMetrics\Services\QueueMetricsQueryService;
use Filament\Actions\Testing\TestAction;
use Illuminate\Contracts\Queue\ClearableQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    config(['cache.stores.landlord' => ['driver' => 'array']]);
    Cache::purge('landlord');

    config(['queue.queues' => ['default' => 'advisingapp-default']]);

    // The queue metrics facade resolves this service from the container, so a stand-in avoids a live metrics store.
    app()->instance(QueueMetricsQueryService::class, new class () {
        public function getQueueMetrics(string $connection, string $queue): QueueMetricsData
        {
            return QueueMetricsData::fromArray(['throughput_per_minute' => 12.5, 'failure_rate' => 7.25]);
        }

        public function getQueueDepth(string $connection, string $queue): QueueDepthData
        {
            return QueueDepthData::fromArray([
                'connection' => $connection,
                'queue' => $queue,
                'pending_jobs' => 7,
                'reserved_jobs' => 2,
                'delayed_jobs' => 1,
                'measured_at' => now()->toIso8601String(),
            ]);
        }
    });

    app()->instance(PickupTimeStoreContract::class, new class () implements PickupTimeStoreContract {
        public function record(string $connection, string $queue, float $timestamp, float $pickupSeconds): void {}

        public function recentSamples(string $connection, string $queue, int $windowSeconds): array
        {
            return array_map(fn (int $second): array => ['timestamp' => microtime(true), 'pickup_seconds' => (float) $second], range(1, 100));
        }
    });
});

it('shows the depth, metrics, provisioned workers and p95 pickup time of each known queue', function () {
    app(WorkerCountHistory::class)->record('advisingapp-default', 4);

    asSuperAdmin();

    livewire(QueueOverviewTable::class)
        ->assertCanSeeTableRecords(['advisingapp-default'])
        ->assertTableColumnStateSet('label', 'default', record: 'advisingapp-default')
        ->assertTableColumnStateSet('pending', 7, record: 'advisingapp-default')
        ->assertTableColumnStateSet('reserved', 2, record: 'advisingapp-default')
        ->assertTableColumnStateSet('delayed', 1, record: 'advisingapp-default')
        ->assertTableColumnFormattedStateSet('failure_rate', '7.3%', record: 'advisingapp-default')
        ->assertTableColumnStateSet('workers', 4, record: 'advisingapp-default')
        ->assertTableColumnFormattedStateSet('p95_pickup_seconds', '95.0s', record: 'advisingapp-default');
});

describe('flush action', function () {
    it('clears the queue and notifies with the number of jobs deleted', function () {
        $clearableQueue = new class () implements ClearableQueue {
            /**
             * @var list<string>
             */
            public array $cleared = [];

            public function clear($queue): int
            {
                $this->cleared[] = $queue;

                return 42;
            }
        };

        asSuperAdmin();

        Queue::partialMock()->shouldReceive('connection')->with(config('queue.default'))->andReturn($clearableQueue);

        livewire(QueueOverviewTable::class)
            ->callAction(TestAction::make('flush')->table('advisingapp-default'))
            ->assertNotified('Flushed default: 42 jobs deleted.');

        expect($clearableQueue->cleared)->toBe(['advisingapp-default']);
    });

    it('refuses to flush when the queue driver cannot clear queues', function () {
        asSuperAdmin();

        Queue::partialMock()->shouldReceive('connection')->with(config('queue.default'))->andReturn(new stdClass());

        livewire(QueueOverviewTable::class)
            ->callAction(TestAction::make('flush')->table('advisingapp-default'))
            ->assertNotified('This queue driver does not support flushing.');
    });
});
