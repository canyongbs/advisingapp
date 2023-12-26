<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use AdvisingApp\InventoryManagement\Models\AssetType;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages\ViewAssetType;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages\ListAssetTypes;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages\CreateAssetType;

class AssetTypeResource extends Resource
{
    protected static ?string $model = AssetType::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

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
            'index' => ListAssetTypes::route('/'),
            'create' => CreateAssetType::route('/create'),
            'view' => ViewAssetType::route('/{record}'),
        ];
    }
}
