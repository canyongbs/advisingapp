<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProspectInteractionTypeDoughnutChart extends ChartReportWidget
{
    protected static ?string $heading = 'Type';

    protected int | string | array $columnSpan = [
        'sm' => 12,
        'md' => 6,
        'lg' => 6,
    ];

    protected static ?string $maxHeight = '240px';

    public function render(): View
    {
        $data = $this->getData()['datasets'][0]['data'];

        $data = $data->filter();

        if (! count($data)) {
            return view('livewire.no-widget-data');
        }

        return view(static::$view, $this->getViewData());
    }

    public function getData(): array
    {
        $interactionsByType = Cache::tags([$this->cacheTag])->remember('prospect_interactions_by_type', now()->addHours(24), function (): Collection {
            $interactionsByTypeData = InteractionType::withCount([
                'interactions' => function ($query) {
                    $query->whereHasMorph(
                        'interactable',
                        Prospect::class,
                    );
                },
            ])->get(['id', 'name']);

            $interactionsByTypeData = $interactionsByTypeData->map(function (InteractionType $interactionType) {
                $interactionType['bg_color'] = $this->getRgbString();

                return $interactionType;
            });

            return $interactionsByTypeData;
        });

        return [
            'labels' => $interactionsByType->pluck('name'),
            'datasets' => [
                [
                    'data' => $interactionsByType->pluck('interactions_count'),
                    'backgroundColor' => $interactionsByType->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    /**
    * @return array<string, mixed>
    */
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

    protected function getRgbString(): string
    {
        return 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
