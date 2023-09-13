<?php

namespace Assist\KnowledgeBase\Filament\Resources;

use Filament\Resources\Resource;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource\Pages;

class KnowledgeBaseStatusResource extends Resource
{
    protected static ?string $model = KnowledgeBaseStatus::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeBaseStatuses::route('/'),
            'create' => Pages\CreateKnowledgeBaseStatus::route('/create'),
            'view' => Pages\ViewKnowledgeBaseStatus::route('/{record}'),
            'edit' => Pages\EditKnowledgeBaseStatus::route('/{record}/edit'),
        ];
    }
}
