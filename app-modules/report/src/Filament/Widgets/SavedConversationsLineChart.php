<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Cache;

class SavedConversationsLineChart extends ChartWidget
{
    protected static ?string $heading = 'Saved Conversations';

    protected $pagePrefix;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 3,
        'lg' => 3,
    ];

    public function mount($pagePrefix = ''): void
    {
        $this->pagePrefix = $pagePrefix;
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

    protected function getData(): array
    {
        $runningTotalPerMonth = Cache::tags([$this->pagePrefix])->rememberForever('saved_conversations_line_chart', function (): array {
            $totalCreatedPerMonth = AiThread::query()
                ->toBase()
                ->selectRaw('date_trunc(\'month\', saved_at) as month')
                ->selectRaw('count(*) as total')
                ->where('saved_at', '>', now()->subYear())
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
