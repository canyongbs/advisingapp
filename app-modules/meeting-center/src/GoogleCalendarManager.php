<?php

namespace Assist\MeetingCenter;

use DateTime;
use Ramsey\Uuid\Uuid;
use Assist\MeetingCenter\Models\Event;
use Assist\MeetingCenter\Contracts\Calendar;
use Spatie\GoogleCalendar\Event as GoogleEvent;

class GoogleCalendarManager implements Calendar
{
    /**
     * @see https://developers.google.com/calendar/api/v3/reference/events/watch
     *
     * @todo watch?
     * @todo multiple calendars?
     * @todo multiple users? one event? multiple events?
     * */
    public static function type(): string
    {
        return 'google';
    }

    public function getEvents(string $calendarId, ?Datetime $start = null, ?Datetime $end = null): array
    {
        /**
         * @todo create without sync?
         * @todo sync uncreated events?
         * */

        return GoogleEvent::get($start ?? now(), $end, ['maxResults' => 2500], $calendarId)->toArray();
    }

    public function createEvent(string $calendarId, Event $event): Event
    {
        /**
         * Warning: If you add an event using the values declined, tentative, or accepted, attendees
         * with the "Add invitations to my calendar" setting set to "When I respond to invitation in email"
         * won't see an event on their calendar unless they choose to change their invitation response in
         * the event invitation email.
         *
         * @see https://developers.google.com/calendar/api/v3/reference/events/insert
         *
         * @todo check for overlapping events
         * @todo add attendees?
         * @todo auto accept?
         * @todo create later?
         * */
        $providerId = Uuid::fromString($event->id)->getHex()->toString();

        GoogleEvent::create([
            'id' => $providerId,
            'summary' => $event->title,
            'description' => $event->description,
            'startDateTime' => $event->starts_at,
            'endDateTime' => $event->ends_at,
        ], $calendarId);

        $event->provider_id = $providerId;
        $event->provider_type = static::type();

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
