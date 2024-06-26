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
        $email_clone_count = Cache::remember('special_actions_doughnut_chart', now()->addMinute(15), function (): array {
            $data_count = array();
            $data_count['email_count'] = AiThread::sum('emailed_count');
            $data_count['clone_count'] = AiThread::sum('cloned_count');
            return $data_count;
        });

        return [
            'labels' => ['Email', 'Clone'],
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => [$email_clone_count['email_count'], $email_clone_count['clone_count']],
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
