<?php

namespace Assist\MeetingCenter\Observers;

use Ramsey\Uuid\Uuid;
use Assist\MeetingCenter\Models\Event;
use Assist\MeetingCenter\GoogleCalendarProvider;

class EventObserver
{
    public function creating(Event $event): void
    {
        $provider = resolve(GoogleCalendarProvider::class);
        $provider->createEvent($event->user->calendar_id, $event);
    }

    public function updating(Event $event): void
    {
        $provider = resolve(GoogleCalendarProvider::class);
        $provider->updateEvent($event->user->calendar_id, $event);
    }

    public function deleting(Event $event): void
    {
        $provider = resolve(GoogleCalendarProvider::class);
        $provider->deleteEvent($event->user->calendar_id, $event);
    }
}
