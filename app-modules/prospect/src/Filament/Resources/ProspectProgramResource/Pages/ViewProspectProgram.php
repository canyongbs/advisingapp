<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectProgramResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Prospect\Filament\Resources\ProspectProgramResource;

class ViewProspectProgram extends ViewRecord
{
    protected static string $resource = ProspectProgramResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Program Name'),
                        TextEntry::make('description'),
                        TextEntry::make('prospectCategories.name')
                            ->label('Category Name'),
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
}
