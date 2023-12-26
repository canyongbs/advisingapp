<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages\CreateAssetStatus;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages\EditAssetStatus;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages\ListAssetStatuses;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages\ViewAssetStatus;
use AdvisingApp\InventoryManagement\Models\AssetStatus;
use Filament\Resources\Resource;

class AssetStatusResource extends Resource
{
    protected static ?string $model = AssetStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetStatuses::route('/'),
            'create' => CreateAssetStatus::route('/create'),
            'view' => ViewAssetStatus::route('/{record}'),
            'edit' => EditAssetStatus::route('/{record}/edit'),
        ];
    }
}
