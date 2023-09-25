<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Interaction\Models\Interaction;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\EditInteraction;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\ListInteractions;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\CreateInteraction;

class InteractionResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = Interaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

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
