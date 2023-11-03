<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class EditCalendarEvent extends EditRecord
{
    protected static string $resource = CalendarEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
