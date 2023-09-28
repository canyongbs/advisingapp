<?php

namespace Assist\KnowledgeBase\Filament\Resources;

use Filament\Resources\Resource;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\EditKnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ViewKnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ListKnowledgeBaseItems;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\CreateKnowledgeBaseItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseItemResource extends Resource
{
    protected static ?string $model = KnowledgeBaseItem::class;

    protected static ?string $navigationLabel = 'Knowledge Base';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Tools';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'question';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['question', 'solution'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['quality', 'status', 'category', 'institution']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Quality' => $record->quality?->name,
            'Status' => $record->status?->name,
            'Category' => $record->category?->name,
            'Institution' => $record->institution->pluck('name')->implode(', '),
        ], fn (mixed $value): bool => filled($value));
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
