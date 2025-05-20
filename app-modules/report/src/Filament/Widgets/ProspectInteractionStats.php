<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class ProspectInteractionStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 12,
        'md' => 6,
        'lg' => 6,
    ];

    public function getStats(): array
    {
        return [
            Stat::make('Total Interactions', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-prospect-interactions-count', now()->addHours(24), function (): int {
                    return Interaction::query()
                        ->whereHasMorph('interactable', Prospect::class)
                        ->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prospects with Interactions', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('prospects-with-interactions', now()->addHours(24), function (): int {
                    return Interaction::query()
                        ->whereHasMorph('interactable', Prospect::class)
                        ->distinct('interactable_id')
                        ->count('interactable_id');
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
