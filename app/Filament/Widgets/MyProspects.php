<?php

namespace App\Filament\Widgets;

use Assist\Prospect\Models\Prospect;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyProspects extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Prospects (Subscribed)',
                $this->formatCount(
                    auth()->user()->subscriptions()->where('subscribable_type', (new Prospect())->getMorphClass())->count()
                )
            ),
        ];
    }
}
