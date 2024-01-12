<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\InventoryManagement\Models\AssetCheckIn;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckInResource\Pages\EditAssetCheckIn;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckInResource\Pages\ViewAssetCheckIn;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckInResource\Pages\ListAssetCheckIns;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckInResource\Pages\CreateAssetCheckIn;

class AssetCheckInResource extends Resource
{
    protected static ?string $model = AssetCheckIn::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getPages(): array
    {
        return [
            'index' => ListAssetCheckIns::route('/'),
            // 'create' => CreateAssetCheckIn::route('/create'),
            // 'view' => ViewAssetCheckIn::route('/{record}'),
            // 'edit' => EditAssetCheckIn::route('/{record}/edit'),
        ];
    }
}
