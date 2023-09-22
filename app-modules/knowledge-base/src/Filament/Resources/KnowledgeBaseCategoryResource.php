<?php

namespace Assist\KnowledgeBase\Filament\Resources;

use Filament\Resources\Resource;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource\Pages;

class KnowledgeBaseCategoryResource extends Resource
{
    protected static ?string $model = KnowledgeBaseCategory::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeBaseCategories::route('/'),
            'create' => Pages\CreateKnowledgeBaseCategory::route('/create'),
            'view' => Pages\ViewKnowledgeBaseCategory::route('/{record}'),
            'edit' => Pages\EditKnowledgeBaseCategory::route('/{record}/edit'),
        ];
    }
}
