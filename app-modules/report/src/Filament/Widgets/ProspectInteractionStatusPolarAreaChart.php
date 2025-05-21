<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProspectInteractionStatusPolarAreaChart extends ChartReportWidget
{
    protected static ?string $heading = 'Status';

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
        $interactionsByStatus = Cache::tags([$this->cacheTag])->remember('prospect_interactions_by_status', now()->addHours(24), function (): Collection {
            $interactionsByStatusData = InteractionStatus::withCount([
                'interactions' => function ($query) {
                    $query->whereHasMorph(
                        'interactable',
                        Prospect::class,
                    );
                },
            ])->get(['id', 'name']);

            $interactionsByTypeData = $interactionsByStatusData->map(function (InteractionStatus $interactionStatus) {
                $interactionStatus['bg_color'] = $interactionStatus->color->getRgbString();

                return $interactionStatus;
            });

            return $interactionsByTypeData;
        });

        return [
            'labels' => $interactionsByStatus->pluck('name'),
            'datasets' => [
                [
                    'data' => $interactionsByStatus->pluck('interactions_count'),
                    'backgroundColor' => $interactionsByStatus->pluck('bg_color'),
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

    protected function getType(): string
    {
        return 'polarArea';
    }
}
