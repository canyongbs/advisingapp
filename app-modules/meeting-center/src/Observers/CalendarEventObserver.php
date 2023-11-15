<?php

namespace Assist\MeetingCenter\Observers;

use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\Managers\CalendarManager;

class CalendarEventObserver
{
    public function created(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->provider_type->value)
                ->createEvent($event);
        }
    }

    // public function updating(CalendarEvent $event): void
    // {
    //     $event->starts_at = $event->starts_at->shiftTimezone('UTC');
    //     $event->ends_at = $event->ends_at->shiftTimezone('UTC');
    // }

    public function updated(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->provider_type->value)
                ->updateEvent($event);
        }
    }

    public function deleted(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->provider_type->value)
                ->deleteEvent($event);
        }
    }
}
