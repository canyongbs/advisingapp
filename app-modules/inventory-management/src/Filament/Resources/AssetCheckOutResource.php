<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\InventoryManagement\Models\AssetCheckOut;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource\Pages\EditAssetCheckOut;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource\Pages\ViewAssetCheckOut;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource\Pages\ListAssetCheckOuts;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource\Pages\CreateAssetCheckOut;

class AssetCheckOutResource extends Resource
{
    protected static ?string $model = AssetCheckOut::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getPages(): array
    {
        return [
            'index' => ListAssetCheckOuts::route('/'),
            // 'create' => CreateAssetCheckOut::route('/create'),
            // 'view' => ViewAssetCheckOut::route('/{record}'),
            // 'edit' => EditAssetCheckOut::route('/{record}/edit'),
        ];
    }
}
