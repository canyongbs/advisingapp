<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Support\Colors\Color;
use AdvisingApp\Ai\Models\AiThread;

class SpecialActionsDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Special Actions';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
    ];

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
        $email_count = AiThread::sum('emailed');
        $clone_count = AiThread::sum('cloned');

        return [
            'labels' => ['Email', 'Clone'],
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => [$email_count, $clone_count],
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
