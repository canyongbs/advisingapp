<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Livewire\Attributes\On;
use AdvisingApp\Ai\Models\Prompt;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class PromptsCreatedLineChart extends ChartWidget
{
    protected static ?string $heading = 'Prompts Created';

    protected static ?string $pollingInterval = null;

    public $pagePrefix;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 3,
        'lg' => 3,
    ];

    public function mount($pagePrefix = ''): void
    {
        $this->pagePrefix = $pagePrefix;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
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
        $runningTotalPerMonth = Cache::tags([$this->pagePrefix])->remember('prompts_created_line_chart', now()->addHours(24), function (): array {
            $totalCreatedPerMonth = Prompt::query()
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
