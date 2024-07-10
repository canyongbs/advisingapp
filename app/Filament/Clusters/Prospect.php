<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Prospect extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Recruitment CRM';

    protected static ?int $navigationSort = 20;
}
