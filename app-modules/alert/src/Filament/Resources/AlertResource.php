<?php

namespace Assist\Alert\Filament\Resources;

use Assist\Alert\Models\Alert;
use Filament\Resources\Resource;
use Assist\Alert\Filament\Resources\AlertResource\Pages;

class AlertResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 5;

    protected static ?string $model = Alert::class;

    protected static ?string $label = 'Proactive Alert';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlerts::route('/'),
        ];
    }
}
