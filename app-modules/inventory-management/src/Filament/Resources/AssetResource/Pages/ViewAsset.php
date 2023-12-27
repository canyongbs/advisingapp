<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('serial_number'),
                        TextEntry::make('name'),
                        TextEntry::make('description'),
                        TextEntry::make('type.name')
                            ->label('Type'),
                        TextEntry::make('location.name')
                            ->label('Location'),
                        TextEntry::make('status.name')
                            ->label('Status'),
                        TextEntry::make('purchase_date'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
