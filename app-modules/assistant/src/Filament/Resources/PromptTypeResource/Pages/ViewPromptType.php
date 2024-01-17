<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;

class ViewPromptType extends ViewRecord
{
    protected static string $resource = PromptTypeResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->columnSpanFull(),
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
