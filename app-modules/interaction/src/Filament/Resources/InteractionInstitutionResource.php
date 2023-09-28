<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Assist\Interaction\Models\InteractionInstitution;
use Assist\Interaction\Filament\Resources\InteractionInstitutionResource\Pages\EditInteractionInstitution;
use Assist\Interaction\Filament\Resources\InteractionInstitutionResource\Pages\ListInteractionInstitutions;
use Assist\Interaction\Filament\Resources\InteractionInstitutionResource\Pages\CreateInteractionInstitution;

class InteractionInstitutionResource extends Resource
{
    protected static ?string $model = InteractionInstitution::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Product Settings';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Institution Name'),
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
            'index' => ListInteractionInstitutions::route('/'),
            'create' => CreateInteractionInstitution::route('/create'),
            'edit' => EditInteractionInstitution::route('/{record}/edit'),
        ];
    }
}
