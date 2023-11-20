<?php

namespace App\Filament\Widgets;

use Assist\Prospect\Models\Prospect;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalProspects extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Prospects', $this->formatCount(Prospect::count())),
        ];
    }
}
