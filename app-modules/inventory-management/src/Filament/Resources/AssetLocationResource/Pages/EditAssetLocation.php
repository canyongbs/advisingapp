<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages;

use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetLocation extends EditRecord
{
    protected static string $resource = AssetLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
