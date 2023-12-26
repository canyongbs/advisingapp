<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages;

use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetStatus extends ViewRecord
{
    protected static string $resource = AssetStatusResource::class;

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
