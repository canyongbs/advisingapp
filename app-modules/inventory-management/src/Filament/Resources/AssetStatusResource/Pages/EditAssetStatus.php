<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages;

use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetStatus extends EditRecord
{
    protected static string $resource = AssetStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
