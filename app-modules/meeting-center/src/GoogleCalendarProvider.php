<?php

namespace Assist\MeetingCenter;

use Ramsey\Uuid\Uuid;
use Assist\MeetingCenter\Models\Event;
use Spatie\GoogleCalendar\Event as GoogleEvent;

class GoogleCalendarProvider extends CalendarProvider
{
    public function type(): string
    {
        return 'google';
    }

    public function getEvents(): array
    {
        return GoogleEvent::get(now(), queryParameters: ['maxResults' => 2500])->toArray();
    }

    public function createEvent(Event $event): Event
    {
        $providerId = Uuid::fromString($event->id)->getHex()->toString();

        $google = GoogleEvent::create([
            'id' => $providerId,
            'summary' => $event->title,
            'description' => $event->description,
            'startDateTime' => $event->starts_at,
            'endDateTime' => $event->ends_at,
        ]);

        $event->provider_id = $providerId;
        $event->provider_type = $this->type();

        return $event;
    }

    public function deleteEvent(Event $event)
    {
        // TODO: Implement deleteEvent() method.
    }

    public function updateEvent(Event $event)
    {
        // TODO: Implement updateEvent() method.
    }
}
