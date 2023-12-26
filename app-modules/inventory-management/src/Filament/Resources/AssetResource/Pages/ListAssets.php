<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

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
