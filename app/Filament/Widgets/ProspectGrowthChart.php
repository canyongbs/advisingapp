<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Assist\Prospect\Models\Prospect;

class ProspectGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Cumulative Prospect Growth';

    protected static ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $totalCreatedPerMonth = Prospect::query()->toBase()
            ->selectRaw('date_trunc(\'month\', created_at) as month')
            ->selectRaw('count(*) as total')
            ->where('created_at', '>', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $runningTotal = Prospect::query()
            ->where('created_at', '<', now()->subYear())
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
                    'label' => 'Prospects',
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
