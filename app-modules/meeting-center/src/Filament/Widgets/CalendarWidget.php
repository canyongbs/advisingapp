<?php

namespace Assist\MeetingCenter\Filament\Widgets;

use App\Models\User;
use Assist\MeetingCenter\Models\CalendarEvent;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $info): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->calendar
            ->events()
            ->get()
            ->map(
                fn (CalendarEvent $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->title)
                    ->start($event->starts_at)
                    ->end($event->ends_at)
                    ->url(CalendarEventResource::getUrl('view', ['record' => $event]), true)
            )
            ->toArray();
    }
}
