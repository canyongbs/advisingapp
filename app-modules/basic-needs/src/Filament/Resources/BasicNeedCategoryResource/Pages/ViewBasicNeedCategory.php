<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource;

class ViewBasicNeedCategory extends ViewRecord
{
    protected static string $resource = BasicNeedCategoryResource::class;

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
