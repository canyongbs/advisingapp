<?php

namespace Assist\Prospect\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

class ProspectStatusResource extends Resource
{
    protected static ?string $model = ProspectStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListProspectStatuses::route('/'),
            'create' => Pages\CreateProspectStatus::route('/create'),
            'view' => Pages\ViewProspectStatus::route('/{record}'),
            'edit' => Pages\EditProspectStatus::route('/{record}/edit'),
        ];
    }
}
