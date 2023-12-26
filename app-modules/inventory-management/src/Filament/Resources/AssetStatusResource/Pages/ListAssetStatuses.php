<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource;

class ListAssetStatuses extends ListRecords
{
    protected static string $resource = AssetStatusResource::class;

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
