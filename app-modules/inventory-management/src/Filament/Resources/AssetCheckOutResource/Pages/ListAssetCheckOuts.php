<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\InventoryManagement\Models\AssetCheckOut;
use AdvisingApp\InventoryManagement\Enums\AssetCheckOutStatus;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource;

class ListAssetCheckOuts extends ListRecords
{
    protected static string $resource = AssetCheckOutResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('asset.name')
                    ->searchable(),
                TextColumn::make('asset.type.name')
                    ->searchable(),
                TextColumn::make('checked_out_at')
                    ->dateTime(),
                TextColumn::make('expected_check_in_at')
                    ->dateTime(),
                TextColumn::make('asset_check_in_id')
                    ->label('Status')
                    ->state(fn (AssetCheckOut $record): AssetCheckOutStatus => $record->status)
                    ->formatStateUsing(fn (AssetCheckOutStatus $state) => $state->getLabel())
                    ->badge()
                    ->color(fn (AssetCheckOutStatus $state): string => match ($state) {
                        AssetCheckOutStatus::Returned => 'success',
                        AssetCheckOutStatus::InGoodStanding => 'info',
                        default => 'danger',
                    }),
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
