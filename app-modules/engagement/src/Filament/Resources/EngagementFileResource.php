<?php

namespace Assist\Engagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Engagement\Models\EngagementFile;
use Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

class EngagementFileResource extends Resource
{
    protected static ?string $model = EngagementFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static bool $shouldRegisterNavigation = false;

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEngagementFiles::route('/'),
            'create' => Pages\CreateEngagementFile::route('/create'),
            'view' => Pages\ViewEngagementFile::route('/{record}'),
            'edit' => Pages\EditEngagementFile::route('/{record}/edit'),
        ];
    }
}
