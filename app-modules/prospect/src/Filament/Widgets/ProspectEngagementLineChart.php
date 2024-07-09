<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Report\Filament\Widgets\ChartReportWidget;

class ProspectEngagementLineChart extends ChartReportWidget
{
    protected static ?string $heading = 'Prospects (Engagement)';

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
        $runningTotalPerMonth = Cache::tags([$this->cacheTag])->remember('prospect_engagements_line_chart', now()->addHours(24), function (): array {
            $totalEmailEnagagementsPerMonth = EngagementDeliverable::query()
                ->whereHas('engagement', function ($q) {
                    return $q->whereHasMorph('recipient', Prospect::class);
                })
                ->toBase()
                ->where('channel', 'email')
                ->where('delivery_status', 'successful')
                ->selectRaw('date_trunc(\'month\', delivered_at) as month')
                ->selectRaw('count(*) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $totalTextEnagagementsPerMonth = EngagementDeliverable::query()
                ->whereHas('engagement', function ($q) {
                    return $q->whereHasMorph('recipient', Prospect::class);
                })
                ->toBase()
                ->where('channel', 'sms')
                ->where('delivery_status', 'successful')
                ->selectRaw('date_trunc(\'month\', delivered_at) as month')
                ->selectRaw('count(*) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $runningTotalPerMonth = [];

            foreach (range(11, 0) as $month) {
                $month = Carbon::now()->subMonths($month);
                $runningTotalPerMonth[$month->format('M Y')] = $totalEmailEnagagementsPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
            }

            return $runningTotalPerMonth;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Email',
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
