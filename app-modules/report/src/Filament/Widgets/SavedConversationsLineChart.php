<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use AdvisingApp\Ai\Models\AiThread;

class SavedConversationsLineChart extends ChartWidget
{
    protected static ?string $heading = 'Saved Conversations';

    protected static ?string $pollingInterval = null;

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
        $totalCreatedPerMonth = AiThread::query()
            ->toBase()
            ->selectRaw('date_trunc(\'month\', saved_at) as month')
            ->selectRaw('count(*) as total')
            ->where('saved_at', '>', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $runningTotal = AiThread::query()
            ->where('saved_at', '<', now()->subYear())
            ->count();

        $runningTotalPerMonth = [];

        foreach (range(11, 0) as $month) {
            $month = Carbon::now()->subMonths($month);

            $runningTotal += $totalCreatedPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;

            $runningTotalPerMonth[$month->format('M Y')] = $runningTotal;
        }

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
