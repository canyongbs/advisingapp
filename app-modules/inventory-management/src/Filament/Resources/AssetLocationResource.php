<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use AdvisingApp\InventoryManagement\Models\AssetLocation;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages\ViewAssetLocation;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages\ListAssetLocations;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages\CreateAssetLocation;

class AssetLocationResource extends Resource
{
    protected static ?string $model = AssetLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Product Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetLocations::route('/'),
            'create' => CreateAssetLocation::route('/create'),
            'view' => ViewAssetLocation::route('/{record}'),
        ];
    }
}
