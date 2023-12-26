<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages\CreateAssetLocation;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages\EditAssetLocation;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages\ListAssetLocations;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetLocationResource\Pages\ViewAssetLocation;
use AdvisingApp\InventoryManagement\Models\AssetLocation;
use Filament\Resources\Resource;

class AssetLocationResource extends Resource
{
    protected static ?string $model = AssetLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetLocations::route('/'),
            'create' => CreateAssetLocation::route('/create'),
            'view' => ViewAssetLocation::route('/{record}'),
            'edit' => EditAssetLocation::route('/{record}/edit'),
        ];
    }
}
