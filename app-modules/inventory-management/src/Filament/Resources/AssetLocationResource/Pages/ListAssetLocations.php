<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource;

class ListAssetLocations extends ListRecords
{
    protected static string $resource = AssetLocationResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
