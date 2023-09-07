<?php

namespace Assist\Interaction\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Filament\Resources\InteractionStatusResource\Pages;

class InteractionStatusResource extends Resource
{
    protected static ?string $model = InteractionStatus::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 13;

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
            'index' => Pages\ListInteractionStatuses::route('/'),
            'create' => Pages\CreateInteractionStatus::route('/create'),
            'edit' => Pages\EditInteractionStatus::route('/{record}/edit'),
        ];
    }
}
