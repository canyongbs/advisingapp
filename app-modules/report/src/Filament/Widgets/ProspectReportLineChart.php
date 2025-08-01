<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Prospect\Models\Prospect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ProspectReportLineChart extends ChartReportWidget
{
    protected static ?string $heading = 'Prospects (Cumulative)';

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $runningTotalPerMonth = $shouldBypassCache
            ? $this->getProspectRunningTotalData($startDate, $endDate)
            : Cache::tags(["{{$this->cacheTag}}"])
                ->remember('total-prospects_line_chart', now()->addHours(24), function (): array {
                    return $this->getProspectRunningTotalData();
                });

        return [
            'datasets' => [
                [
                    'data' => array_values($runningTotalPerMonth),
                ],
            ],
            'labels' => array_keys($runningTotalPerMonth),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, int>
     */
    protected function getProspectRunningTotalData(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $months = $this->getMonthRange($startDate, $endDate);

        $monthlyData = Prospect::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("date_trunc('month', created_at) as month, COUNT(*) as monthly_total")
            ->groupByRaw("date_trunc('month', created_at)")
            ->get()
            ->mapWithKeys(function (object $item): array {
                return [
                    Carbon::parse($item['month'])->startOfMonth()->toDateString() => (int) $item['monthly_total'],
                ];
            });

        $runningTotal = [];
        $total = 0;

        foreach ($months as $month) {
            $key = $month->toDateString();
            $label = $month->format('M Y');
            $count = $monthlyData[$key] ?? 0;
            $total += $count;
            $runningTotal[$label] = $total;
        }

        return $runningTotal;
    }
}
