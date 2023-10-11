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
        $provider->createEvent($event);
    }

    public function created(Event $event): void
    {
        // ray(\Spatie\GoogleCalendar\Event::create([
        //     'id' => Uuid::fromString($event->id)->getHex()->toString(),
        //     'summary' => $event->title,
        //     'description' => $event->description,
        //     'startDateTime' => $event->starts_at,
        //     'endDateTime' => $event->ends_at,
        // ]));
    }
}
