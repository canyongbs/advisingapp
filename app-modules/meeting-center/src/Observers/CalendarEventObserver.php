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
                ->driver($event->calendar->type)
                ->createEvent($event);
        }
    }

    public function updated(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->type)
                ->updateEvent($event);
        }
    }

    public function deleted(CalendarEvent $event): void
    {
        if ($event->calendar) {
            resolve(CalendarManager::class)
                ->driver($event->calendar->type)
                ->deleteEvent($event);
        }
    }
}
