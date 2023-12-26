<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource;

class CreateAssetStatus extends CreateRecord
{
    protected static string $resource = AssetStatusResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }
}
