<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource\Pages;

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewBasicNeedProgram extends ViewRecord
{
    protected static string $resource = BasicNeedProgramResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Program Name'),
                        TextEntry::make('description'),
                        TextEntry::make('basicNeedCategories.name')
                            ->label('Program Category'),
                        TextEntry::make('contact_person')
                            ->label('Contact Person'),
                        TextEntry::make('contact_email')
                            ->label('Email Address'),
                        TextEntry::make('contact_phone')
                            ->label('Contact Phone'),
                        TextEntry::make('location')
                            ->label('Location'),
                        TextEntry::make('availability')
                            ->label('Availability'),
                        TextEntry::make('eligibility_criteria')
                            ->label('Eligibility Criteria'),
                        TextEntry::make('application_process')
                            ->label('Application Process'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
