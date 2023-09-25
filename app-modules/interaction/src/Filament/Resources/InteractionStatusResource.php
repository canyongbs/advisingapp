<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Assist\Interaction\Models\InteractionStatus;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\Interaction\Enums\InteractionStatusColorOptions;
use Assist\Interaction\Filament\Resources\InteractionStatusResource\Pages\EditInteractionStatus;
use Assist\Interaction\Filament\Resources\InteractionStatusResource\Pages\CreateInteractionStatus;
use Assist\Interaction\Filament\Resources\InteractionStatusResource\Pages\ListInteractionStatuses;

class InteractionStatusResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = InteractionStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Status Name'),
                Select::make('color')
                    ->label('Color')
                    ->translateLabel()
                    ->searchable()
                    ->options(InteractionStatusColorOptions::class)
                    ->required()
                    ->enum(InteractionStatusColorOptions::class),
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
            'index' => ListInteractionStatuses::route('/'),
            'create' => CreateInteractionStatus::route('/create'),
            'edit' => EditInteractionStatus::route('/{record}/edit'),
        ];
    }
}
