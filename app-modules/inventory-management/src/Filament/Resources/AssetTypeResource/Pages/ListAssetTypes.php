<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource;

class ListAssetTypes extends ListRecords
{
    protected static string $resource = AssetTypeResource::class;

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
