<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Filament\Resources\InteractionOutcomeResource\Pages;

class InteractionOutcomeResource extends Resource
{
    protected static ?string $model = InteractionOutcome::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 12;

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
            'index' => Pages\ListInteractionOutcomes::route('/'),
            'create' => Pages\CreateInteractionOutcome::route('/create'),
            'edit' => Pages\EditInteractionOutcome::route('/{record}/edit'),
        ];
    }
}
