<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource;

class CreateAssetLocation extends CreateRecord
{
    protected static string $resource = AssetLocationResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }
}
