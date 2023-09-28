<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Interaction\Models\Interaction;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\EditInteraction;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\ListInteractions;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\CreateInteraction;

class InteractionResource extends Resource
{
    protected static ?string $model = Interaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 5;

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInteractions::route('/'),
            'create' => CreateInteraction::route('/create'),
            'edit' => EditInteraction::route('/{record}/edit'),
        ];
    }
}
