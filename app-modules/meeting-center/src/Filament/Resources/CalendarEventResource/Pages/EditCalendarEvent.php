<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
