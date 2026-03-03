<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use UnitEnum;

class EarlyAlerts extends Cluster
{
    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 60;
}
