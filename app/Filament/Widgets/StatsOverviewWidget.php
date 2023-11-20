<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\FormatsCount;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    use FormatsCount;

    protected int | string | array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }
}
