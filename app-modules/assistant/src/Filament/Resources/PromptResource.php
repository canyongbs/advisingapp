<?php

namespace AdvisingApp\Assistant\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\Assistant\Models\Prompt;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\EditPrompt;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\ViewPrompt;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\ListPrompts;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\CreatePrompt;

class PromptResource extends Resource
{
    protected static ?string $model = Prompt::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?string $navigationLabel = 'Prompt Library';

    protected static ?int $navigationSort = 10;

    public static function getPages(): array
    {
        return [
            'index' => ListPrompts::route('/'),
            'create' => CreatePrompt::route('/create'),
            'view' => ViewPrompt::route('/{record}'),
            'edit' => EditPrompt::route('/{record}/edit'),
        ];
    }
}
