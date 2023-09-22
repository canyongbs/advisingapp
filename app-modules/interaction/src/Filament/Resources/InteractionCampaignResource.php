<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Filament\Resources\InteractionCampaignResource\Pages\EditInteractionCampaign;
use Assist\Interaction\Filament\Resources\InteractionCampaignResource\Pages\ListInteractionCampaigns;
use Assist\Interaction\Filament\Resources\InteractionCampaignResource\Pages\CreateInteractionCampaign;

class InteractionCampaignResource extends Resource
{
    protected static ?string $model = InteractionCampaign::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Campaign Name'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInteractionCampaigns::route('/'),
            'create' => CreateInteractionCampaign::route('/create'),
            'edit' => EditInteractionCampaign::route('/{record}/edit'),
        ];
    }
}
