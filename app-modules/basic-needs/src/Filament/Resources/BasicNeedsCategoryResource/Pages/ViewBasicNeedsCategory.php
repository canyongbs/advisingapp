<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource;

class ViewBasicNeedsCategory extends ViewRecord
{
    protected static string $resource = BasicNeedsCategoryResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Category Name'),
                        TextEntry::make('description')
                            ->label('Description'),
                    ])
                    ->columns(1),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
