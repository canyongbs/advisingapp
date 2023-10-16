<?php

namespace Assist\MeetingCenter\Observers;

use Assist\MeetingCenter\CalendarManager;
use Assist\MeetingCenter\Models\CalendarEvent;

class CalendarEventObserver
{
    public function creating(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->type)
                ->createEvent($event);
        }
    }

    public function updating(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->type)
                ->updateEvent($event);
        }
    }

    public function deleting(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->type)
                ->deleteEvent($event);
        }
    }
}
