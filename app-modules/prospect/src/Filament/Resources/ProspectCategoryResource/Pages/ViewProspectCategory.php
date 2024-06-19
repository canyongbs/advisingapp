<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource;

class ViewProspectCategory extends ViewRecord
{
    protected static string $resource = ProspectCategoryResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Category Name')
                            ->translateLabel(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->translateLabel(),
                    ])
                    ->columns(1),
            ]);
    }
}
