<?php

namespace AdvisingApp\Alert\Filament\Resources\AlertStatusResource\Pages;

use AdvisingApp\Alert\Filament\Resources\AlertStatusResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAlertStatus extends ViewRecord
{
    protected static string $resource = AlertStatusResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('classification')
                            ->label('Classification'),
                        TextEntry::make('sort')
                            ->numeric(),
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
