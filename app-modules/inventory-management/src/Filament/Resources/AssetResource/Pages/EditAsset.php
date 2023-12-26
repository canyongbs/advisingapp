<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
