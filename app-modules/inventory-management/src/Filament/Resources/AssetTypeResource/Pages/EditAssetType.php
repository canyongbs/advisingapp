<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages;

use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetType extends EditRecord
{
    protected static string $resource = AssetTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
