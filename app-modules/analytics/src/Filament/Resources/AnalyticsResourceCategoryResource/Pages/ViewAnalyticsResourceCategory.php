<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource;

class ViewAnalyticsResourceCategory extends ViewRecord
{
    protected static string $resource = AnalyticsResourceCategoryResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('classification'),
                        TextEntry::make('description'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
