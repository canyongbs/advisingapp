<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class UnifiedInbox extends Cluster
{
    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = 'Unified Inbox';
}
