<?php

namespace AdvisingApp\Assistant\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\Assistant\Models\AiAssistant;
use App\Filament\Clusters\ArtificialIntelligence;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\EditAiAssistant;
use AdvisingApp\Assistant\Filament\Resources\AiAssistantResource\Pages\ListAiAssistants;
use AdvisingApp\Assistant\Filament\Resources\AiAssistantResource\Pages\CreateAiAssistant;

class AiAssistantResource extends Resource
{
    protected static ?string $model = AiAssistant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = ArtificialIntelligence::class;

    public static function getPages(): array
    {
        return [
            'index' => ListAiAssistants::route('/'),
            'create' => CreateAiAssistant::route('/create'),
            'edit' => EditAiAssistant::route('/{record}/edit'),
        ];
    }
}
