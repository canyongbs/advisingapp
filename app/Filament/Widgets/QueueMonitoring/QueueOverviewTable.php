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

namespace App\Filament\Widgets\QueueMonitoring;

use App\Filament\Pages\QueueMonitoring;
use App\Filament\Widgets\QueueMonitoring\Actions\FlushQueueAction;
use App\Support\QueueAutoscale\WorkerCountHistory;
use App\Support\QueueMonitoring\KnownQueues;
use Cbox\LaravelQueueAutoscale\Contracts\PercentileCalculatorContract;
use Cbox\LaravelQueueAutoscale\Contracts\PickupTimeStoreContract;
use Cbox\LaravelQueueMetrics\Facades\QueueMetrics;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Config;

class QueueOverviewTable extends TableWidget
{
    private const int PICKUP_WINDOW_SECONDS = 300;

    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Queues';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return QueueMonitoring::canAccess();
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => $this->queues())
            ->paginated(false)
            ->columns([
                TextColumn::make('label')
                    ->label('Queue'),
                TextColumn::make('pending')
                    ->numeric(),
                TextColumn::make('reserved')
                    ->numeric(),
                TextColumn::make('delayed')
                    ->numeric(),
                TextColumn::make('throughput_per_minute')
                    ->label('Throughput/min')
                    ->numeric(decimalPlaces: 1),
                TextColumn::make('failure_rate')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1) . '%')
                    ->color(fn (float $state): string => $state > 5.0 ? 'danger' : 'gray'),
                TextColumn::make('workers')
                    ->numeric(),
                TextColumn::make('p95_pickup_seconds')
                    ->label('p95 Pickup')
                    ->placeholder('—')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1) . 's'),
            ])
            ->recordActions([
                FlushQueueAction::make(),
            ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function queues(): array
    {
        $connection = Config::string('queue.default');
        $knownQueues = app(KnownQueues::class);
        $workerCountHistory = app(WorkerCountHistory::class);
        $pickupTimeStore = app(PickupTimeStoreContract::class);
        $percentileCalculator = app(PercentileCalculatorContract::class);

        $queues = [];

        foreach ($knownQueues->names() as $queue) {
            $depth = QueueMetrics::getQueueDepth($connection, $queue);
            $metrics = QueueMetrics::getQueueMetrics($connection, $queue);

            $pickupSeconds = array_column($pickupTimeStore->recentSamples($connection, $queue, self::PICKUP_WINDOW_SECONDS), 'pickup_seconds');

            $queues[$queue] = [
                'name' => $queue,
                'label' => $knownQueues->label($queue),
                'pending' => $depth->pendingJobs,
                'reserved' => $depth->reservedJobs,
                'delayed' => $depth->delayedJobs,
                'throughput_per_minute' => $metrics->throughputPerMinute,
                'failure_rate' => $metrics->failureRate,
                // The autoscaler's provisioned count, so it matches the chart and an idle worker still counts.
                'workers' => $workerCountHistory->latest($queue) ?? 0,
                'p95_pickup_seconds' => $percentileCalculator->compute($pickupSeconds, 95),
            ];
        }

        return $queues;
    }
}
