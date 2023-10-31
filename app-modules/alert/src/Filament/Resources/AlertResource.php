<?php

namespace Assist\Alert\Filament\Resources;

use Assist\Alert\Models\Alert;
use Filament\Resources\Resource;
use Assist\Alert\Filament\Resources\AlertResource\Pages;

class AlertResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 4;

    protected static ?string $model = Alert::class;

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlerts::route('/'),
            'create' => Pages\CreateAlert::route('/create'),
            //'view' => Pages\ViewAlert::route('/{record}'),
            'edit' => Pages\EditAlert::route('/{record}/edit'),
        ];
    }
}
