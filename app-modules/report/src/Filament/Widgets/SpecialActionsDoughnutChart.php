<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Support\Colors\Color;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Cache;

class SpecialActionsDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Special Actions';

    protected static ?string $pollingInterval = null;

    protected $pagePrefix;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
    ];

    public function mount($pagePrefix = ''): void
    {
        $this->pagePrefix = $pagePrefix;
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $emailCount = Cache::tags([$this->pagePrefix])->rememberForever('emailed_count', function (): int {
            $emailDataCount = AiThread::sum('emailed_count');

            return $emailDataCount;
        });
        $cloneCount = Cache::tags([$this->pagePrefix])->rememberForever('cloned_count', function (): int {
            $cloneDataCount = AiThread::sum('cloned_count');

            return $cloneDataCount;
        });

        return [
            'labels' => ['Email', 'Clone'],
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => [$emailCount, $cloneCount],
                    'backgroundColor' => [$this->getRgbString(Color::Indigo[500]), $this->getRgbString(Color::Emerald[500])],
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getRgbString($color): string
    {
        return "rgb({$color})";
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
