<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource;

class CreateAssetType extends CreateRecord
{
    protected static string $resource = AssetTypeResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
