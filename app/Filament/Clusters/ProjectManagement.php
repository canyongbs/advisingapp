<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ProjectManagement extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 120;
}
