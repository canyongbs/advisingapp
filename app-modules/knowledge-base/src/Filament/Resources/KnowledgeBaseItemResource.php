<?php

namespace Assist\KnowledgeBase\Filament\Resources;

use Filament\Resources\Resource;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\EditKnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ViewKnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ListKnowledgeBaseItems;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\CreateKnowledgeBaseItem;

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
            'index' => ListKnowledgeBaseItems::route('/'),
            'create' => CreateKnowledgeBaseItem::route('/create'),
            'view' => ViewKnowledgeBaseItem::route('/{record}'),
            'edit' => EditKnowledgeBaseItem::route('/{record}/edit'),
        ];
    }
}
