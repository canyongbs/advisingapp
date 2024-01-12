<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckInResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckInResource;

class ListAssetCheckIns extends ListRecords
{
    protected static string $resource = AssetCheckInResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('asset.name')
                    ->searchable(),
                TextColumn::make('asset.type.name')
                    ->searchable(),
                TextColumn::make('checked_in_at')
                    ->dateTime(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
