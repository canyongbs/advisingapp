<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class ViewCalendarEvent extends ViewRecord
{
    protected static string $resource = CalendarEventResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('description'),
                        TextEntry::make('starts_at'),
                        TextEntry::make('ends_at'),
                        TextEntry::make('attendees')
                            ->badge(),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
