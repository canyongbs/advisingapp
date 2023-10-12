<?php

namespace Assist\MeetingCenter;

use Ramsey\Uuid\Uuid;
use Assist\MeetingCenter\Models\Event;
use Spatie\GoogleCalendar\Event as GoogleEvent;

class GoogleCalendarProvider extends CalendarProvider
{
    // TODO: watch?
    // https://developers.google.com/calendar/api/v3/reference/events/watch

    public static function type(): string
    {
        return 'google';
    }

    public function getEvents(string $calendarId): array
    {
        return GoogleEvent::get(now(), queryParameters: ['maxResults' => 2500], calendarId: $calendarId)->toArray();
    }

    public function createEvent(string $calendarId, Event $event): Event
    {
        $providerId = Uuid::fromString($event->id)->getHex()->toString();

        GoogleEvent::create([
            'id' => $providerId,
            'summary' => $event->title,
            'description' => $event->description,
            'startDateTime' => $event->starts_at,
            'endDateTime' => $event->ends_at,
        ], $calendarId);

        $event->provider_id = $providerId;
        $event->provider_type = $this->type();

        return $event;
    }

    public function updateEvent(string $calendarId, Event $event): void
    {
        GoogleEvent::find($event->provider_id, $calendarId)?->update([
            'summary' => $event->title,
            'description' => $event->description,
            'startDateTime' => $event->starts_at,
            'endDateTime' => $event->ends_at,
        ]);
    }

    public function deleteEvent(string $calendarId, Event $event): void
    {
        GoogleEvent::find($event->provider_id, $calendarId)?->delete();
    }
}
