<?php

namespace Assist\MeetingCenter\Observers;

use Assist\MeetingCenter\Models\Event;
use Assist\MeetingCenter\CalendarManager;

class EventObserver
{
    public function creating(Event $event): void
    {
        if ($event->user->calendar_type && $event->user->calendar_id) {
            resolve(CalendarManager::class)
                ->driver($event->user->calendar_type)
                ->createEvent($event->user->calendar_id, $event);
        }
    }

    public function updating(Event $event): void
    {
        if ($event->user->calendar_type && $event->user->calendar_id) {
            resolve(CalendarManager::class)
                ->driver($event->user->calendar_type)
                ->updateEvent($event->user->calendar_id, $event);
        }
    }

    public function deleting(Event $event): void
    {
        if ($event->user->calendar_type && $event->user->calendar_id) {
            resolve(CalendarManager::class)
                ->driver($event->user->calendar_type)
                ->deleteEvent($event->user->calendar_id, $event);
        }
    }
}
