<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\Prospect\Filament\Resources\ProspectResource;

class ManageProspectAlerts extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'alerts';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Alerts';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Alerts';

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
}
