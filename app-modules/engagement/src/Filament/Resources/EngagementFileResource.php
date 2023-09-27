<?php

namespace Assist\Engagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Engagement\Models\EngagementFile;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

class EngagementFileResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = EngagementFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Files and Documents';

    protected ?string $heading = 'Files and Documents';

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
