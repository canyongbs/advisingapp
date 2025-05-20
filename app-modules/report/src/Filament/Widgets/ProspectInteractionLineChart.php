<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ProspectInteractionLineChart extends LineChartReportWidget
{
    protected static ?string $heading = 'Prospects (Interaction)';

    protected int | string | array $columnSpan = 'full';

    public function getOptions(): array
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

    public function getData(): array
    {
        $runningTotalPerMonth = Cache::tags([$this->cacheTag])->remember('prospect_interactions_line_chart', now()->addHours(24), function (): array {
            $totalInteractionPerMonth = Interaction::query()
                ->whereHasMorph('interactable', Prospect::class)
                ->toBase()
                ->selectRaw('date_trunc(\'month\', created_at) as month')
                ->selectRaw('count(*) as total')
                ->where('created_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $runningTotalPerMonth = [];

            foreach (range(11, 0) as $month) {
                $month = Carbon::now()->subMonths($month);
                $runningTotalPerMonth[$month->format('M Y')] = $totalInteractionPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
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
}
