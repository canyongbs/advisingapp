<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use AdvisingApp\InventoryManagement\Models\AssetStatus;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages\ViewAssetStatus;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages\CreateAssetStatus;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages\ListAssetStatuses;

class AssetStatusResource extends Resource
{
    protected static ?string $model = AssetStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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
            'index' => ListAssetStatuses::route('/'),
            'create' => CreateAssetStatus::route('/create'),
            'view' => ViewAssetStatus::route('/{record}'),
        ];
    }
}
