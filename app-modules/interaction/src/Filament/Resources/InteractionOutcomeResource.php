<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Filament\Resources\InteractionOutcomeResource\Pages\EditInteractionOutcome;
use Assist\Interaction\Filament\Resources\InteractionOutcomeResource\Pages\ListInteractionOutcomes;
use Assist\Interaction\Filament\Resources\InteractionOutcomeResource\Pages\CreateInteractionOutcome;

class InteractionOutcomeResource extends Resource
{
    protected static ?string $model = InteractionOutcome::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Outcome Name'),
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
            'index' => ListInteractionOutcomes::route('/'),
            'create' => CreateInteractionOutcome::route('/create'),
            'edit' => EditInteractionOutcome::route('/{record}/edit'),
        ];
    }
}
