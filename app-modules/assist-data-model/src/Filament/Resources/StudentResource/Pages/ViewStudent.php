<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Notifications\Filament\Actions\SubscribeHeaderAction;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('first')
                            ->label('First Name'),
                        TextEntry::make('last')
                            ->label('Last Name'),
                        TextEntry::make('preferred')
                            ->label('Preferred Name')
                            ->default('N/A'),
                        TextEntry::make('otherid')
                            ->label('Other ID'),
                        TextEntry::make('email')
                            ->label('Email Address'),
                        TextEntry::make('sisid')
                            ->label('Student ID'),
                        TextEntry::make('mobile'),
                        TextEntry::make('address'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            SubscribeHeaderAction::make(),
        ];
    }
}
