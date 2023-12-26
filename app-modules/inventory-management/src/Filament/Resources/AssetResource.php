<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\EditAsset;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\ViewAsset;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\ListAssets;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\CreateAsset;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
