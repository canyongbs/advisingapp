<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Filament\Resources\InteractionTypeResource\Pages;

class InteractionTypeResource extends Resource
{
    protected static ?string $model = InteractionType::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListInteractionTypes::route('/'),
            'create' => Pages\CreateInteractionType::route('/create'),
            'edit' => Pages\EditInteractionType::route('/{record}/edit'),
        ];
    }
}
