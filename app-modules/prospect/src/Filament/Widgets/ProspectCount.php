<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use Illuminate\Support\Number;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProspectCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Prospects', Number::abbreviate(Prospect::count(), maxPrecision: 2)),
        ];
    }
}
