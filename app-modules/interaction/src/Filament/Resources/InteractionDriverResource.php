<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Assist\Interaction\Models\InteractionDriver;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\Interaction\Filament\Resources\InteractionDriverResource\Pages\EditInteractionDriver;
use Assist\Interaction\Filament\Resources\InteractionDriverResource\Pages\ListInteractionDrivers;
use Assist\Interaction\Filament\Resources\InteractionDriverResource\Pages\CreateInteractionDriver;

class InteractionDriverResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = InteractionDriver::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Driver Name'),
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
            'index' => ListInteractionDrivers::route('/'),
            'create' => CreateInteractionDriver::route('/create'),
            'edit' => EditInteractionDriver::route('/{record}/edit'),
        ];
    }
}
