<?php

namespace AdvisingApp\Assistant\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\Assistant\Models\PromptType;
use App\Filament\Clusters\ArtificialIntelligence;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\EditPromptType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\ViewPromptType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\ListPromptTypes;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\CreatePromptType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\RelationManagers\PromptsRelationManager;

class PromptTypeResource extends Resource
{
    protected static ?string $model = PromptType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 20;

    protected static ?string $cluster = ArtificialIntelligence::class;

    public static function getRelations(): array
    {
        return [
            PromptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromptTypes::route('/'),
            'create' => CreatePromptType::route('/create'),
            'view' => ViewPromptType::route('/{record}'),
            'edit' => EditPromptType::route('/{record}/edit'),
        ];
    }
}
