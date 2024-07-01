<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use AdvisingApp\Ai\Models\PromptType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class PromptsByCategoryDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Prompts by Category';

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
        $promptsByCategory = Cache::tags([$this->pagePrefix])->rememberForever('prompt_by_category_chart', function (): Collection {
            $promptsByCategoryData = PromptType::withCount(['prompts'])->get(['id', 'title']);

            $promptsByCategoryData = $promptsByCategoryData->map(function (PromptType $promptType) {
                $promptType['bg_color'] = $this->getRgbString();

                return $promptType;
            });

            return $promptsByCategoryData;
        });

        return [
            'labels' => $promptsByCategory->pluck('title'),
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => $promptsByCategory->pluck('prompts_count'),
                    'backgroundColor' => $promptsByCategory->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getRgbString(): string
    {
        return 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
