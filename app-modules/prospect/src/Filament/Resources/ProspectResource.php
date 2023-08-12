<?php

namespace Assist\Prospect\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Prospect\Models\Prospect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementFilesRelationManager;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getRelations(): array
    {
        return [
            EngagementFilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspects::route('/'),
            'create' => Pages\CreateProspect::route('/create'),
            'view' => Pages\ViewProspect::route('/{record}'),
            'edit' => Pages\EditProspect::route('/{record}/edit'),
        ];
    }
}
