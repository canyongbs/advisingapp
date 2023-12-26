<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\InventoryManagement\Models\AssetType;
use AdvisingApp\InventoryManagement\Models\AssetStatus;
use AdvisingApp\InventoryManagement\Models\AssetLocation;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required(),
                TextInput::make('serial_number')
                    ->required(),
                Select::make('type_id')
                    ->relationship('type', 'name')
                    ->preload()
                    ->label('Type')
                    ->required()
                    ->exists((new AssetType())->getTable(), 'id'),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->preload()
                    ->label('Status')
                    ->required()
                    ->exists((new AssetStatus())->getTable(), 'id'),
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->preload()
                    ->label('Location')
                    ->required()
                    ->exists((new AssetLocation())->getTable(), 'id'),
                DatePicker::make('purchase_date')
                    ->required(),
            ]);
    }
}
