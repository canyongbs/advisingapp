<?php

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetStatusResource;

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

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
