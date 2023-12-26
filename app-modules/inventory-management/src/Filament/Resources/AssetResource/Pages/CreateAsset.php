<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }
}
