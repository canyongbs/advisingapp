<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Assist\Interaction\Models\InteractionType;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\Interaction\Filament\Resources\InteractionTypeResource\Pages\EditInteractionType;
use Assist\Interaction\Filament\Resources\InteractionTypeResource\Pages\ListInteractionTypes;
use Assist\Interaction\Filament\Resources\InteractionTypeResource\Pages\CreateInteractionType;

class InteractionTypeResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = InteractionType::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Type Name'),
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
            'index' => ListInteractionTypes::route('/'),
            'create' => CreateInteractionType::route('/create'),
            'edit' => EditInteractionType::route('/{record}/edit'),
        ];
    }
}
