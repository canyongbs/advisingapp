<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\InventoryManagement\Models\AssetType;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages\EditAssetType;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages\ViewAssetType;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages\ListAssetTypes;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages\CreateAssetType;

class AssetTypeResource extends Resource
{
    protected static ?string $model = AssetType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetTypes::route('/'),
            'create' => CreateAssetType::route('/create'),
            'view' => ViewAssetType::route('/{record}'),
            'edit' => EditAssetType::route('/{record}/edit'),
        ];
    }
}
