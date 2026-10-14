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
use App\Support\QueueAutoscale\WorkerCountHistory;
use App\Support\QueueMonitoring\KnownQueues;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Date;

class QueueWorkerCountChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Workers per Queue';

    protected ?string $pollingInterval = '60s';

    protected ?string $maxHeight = '260px';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return QueueMonitoring::canAccess();
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $knownQueues = app(KnownQueues::class);

        $aligned = app(WorkerCountHistory::class)->alignedSeries($knownQueues->names());

        $datasets = [];

        foreach ($aligned['counts'] as $queue => $counts) {
            $color = $this->colorFor($queue);

            $datasets[] = [
                'label' => $queue,
                'data' => $counts,
                'borderColor' => $color,
                'backgroundColor' => $color,
                // A worker count is a step function; spanning the gaps other queues' timestamps leave keeps it one line.
                'stepped' => true,
                'spanGaps' => true,
                'pointRadius' => 2,
                'borderWidth' => 2,
                'fill' => false,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => array_map(
                fn (int $timestamp): string => Date::createFromTimestamp($timestamp)->format('H:i'),
                $aligned['timestamps'],
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ];
    }

    /**
     * A hue derived from the queue name, so each queue keeps its color however many queues are charted. Scaling by
     * the golden angle spreads names that share a prefix, whose hashes are close, around the hue wheel.
     */
    private function colorFor(string $queue): string
    {
        $hue = (int) floor(fmod(crc32($queue) * 137.508, 360.0));

        return "hsl({$hue}, 70%, 45%)";
    }
}
