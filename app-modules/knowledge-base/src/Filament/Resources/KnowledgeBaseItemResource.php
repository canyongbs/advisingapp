<?php

namespace Assist\KnowledgeBase\Filament\Resources;

use Filament\Resources\Resource;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

class KnowledgeBaseItemResource extends Resource
{
    protected static ?string $model = KnowledgeBaseItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeBaseItems::route('/'),
            'create' => Pages\CreateKnowledgeBaseItem::route('/create'),
            'view' => Pages\ViewKnowledgeBaseItem::route('/{record}'),
            'edit' => Pages\EditKnowledgeBaseItem::route('/{record}/edit'),
        ];
    }
}
