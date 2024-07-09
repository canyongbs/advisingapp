<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Report\Filament\Widgets\ChartReportWidget;

class StudentEngagementLineChart extends ChartReportWidget
{
    protected static ?string $heading = 'Students (Engagement)';

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 3,
        'lg' => 3,
    ];

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

    protected function getData(): array
    {
        $runningTotalPerMonth = Cache::tags([$this->cacheTag])->remember('saved_conversations_line_chart', now()->addHours(24), function (): array {
            $totalCreatedPerMonth = EngagementDeliverable::query()
                ->toBase()
                ->selectRaw('date_trunc(\'month\', delivered_at) as month')
                ->selectRaw('count(*) as total')
                ->where('delivered_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $runningTotalPerMonth = [];

            foreach (range(11, 0) as $month) {
                $month = Carbon::now()->subMonths($month);
                $runningTotalPerMonth[$month->format('M Y')] = $totalCreatedPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
            }

            return $runningTotalPerMonth;
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

    protected function getType(): string
    {
        return 'line';
    }
}
