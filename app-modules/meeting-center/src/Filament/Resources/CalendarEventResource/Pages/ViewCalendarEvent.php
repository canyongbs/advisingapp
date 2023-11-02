<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCalendarEvent extends ViewRecord
{
    protected static string $resource = CalendarEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
