<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Contracts\Support\Htmlable;
use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Models\AssetType;
use AdvisingApp\InventoryManagement\Models\AssetStatus;
use AdvisingApp\InventoryManagement\Models\AssetLocation;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\ViewAsset;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\ListAssets;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\CreateAsset;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationLabel = 'Asset Management';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 30;

    protected static ?string $breadcrumb = 'Asset Management';

    public function getTitle(): string | Htmlable
    {
        return 'Manage Assets';
    }

    public static function form(Form $form): Form
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

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
        ];
    }
}
