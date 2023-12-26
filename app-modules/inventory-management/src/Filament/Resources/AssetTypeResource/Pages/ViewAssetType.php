<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetTypeResource;

class ViewAssetType extends ViewRecord
{
    protected static string $resource = AssetTypeResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);
    }
}
