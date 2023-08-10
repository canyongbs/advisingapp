<?php

namespace Assist\KnowledgeBase\Filament\Resources;

use Filament\Resources\Resource;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource\Pages;

class KnowledgeBaseQualityResource extends Resource
{
    protected static ?string $model = KnowledgeBaseQuality::class;

    protected static ?string $navigationGroup = 'Field Settings';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeBaseQualities::route('/'),
            'create' => Pages\CreateKnowledgeBaseQuality::route('/create'),
            'view' => Pages\ViewKnowledgeBaseQuality::route('/{record}'),
            'edit' => Pages\EditKnowledgeBaseQuality::route('/{record}/edit'),
        ];
    }
}
