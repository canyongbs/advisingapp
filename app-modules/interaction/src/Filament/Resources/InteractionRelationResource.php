<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Assist\Interaction\Models\InteractionRelation;
use Assist\Interaction\Filament\Resources\InteractionRelationResource\Pages\EditInteractionRelation;
use Assist\Interaction\Filament\Resources\InteractionRelationResource\Pages\ListInteractionRelations;
use Assist\Interaction\Filament\Resources\InteractionRelationResource\Pages\CreateInteractionRelation;

class InteractionRelationResource extends Resource
{
    protected static ?string $model = InteractionRelation::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Product Settings';

    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Interaction Relation Name'),
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
            'index' => ListInteractionRelations::route('/'),
            'create' => CreateInteractionRelation::route('/create'),
            'edit' => EditInteractionRelation::route('/{record}/edit'),
        ];
    }
}
