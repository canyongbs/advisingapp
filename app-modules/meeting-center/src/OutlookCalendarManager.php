<?php

namespace Assist\MeetingCenter;

use DateTime;
use App\Models\User;
use Assist\MeetingCenter\Models\Event;
use Assist\MeetingCenter\Contracts\Calendar;

class OutlookCalendarManager implements Calendar
{
    // https://github.com/microsoftgraph/msgraph-sample-phpapp/tree/main
    // https://github.com/microsoftgraph/msgraph-sdk-php
    // https://learn.microsoft.com/en-us/graph/api/resources/webhooks?view=graph-rest-1.0

    public static function type(): string
    {
        return 'outlook';
    }

    public function getEvents(string $calendarId, ?Datetime $start = null, ?Datetime $end = null): array
    {
        // https://learn.microsoft.com/en-us/graph/api/user-list-calendars?view=graph-rest-1.0&tabs=http
        // https://learn.microsoft.com/en-us/graph/api/user-list-events?view=graph-rest-1.0&tabs=http

        // TODO: Implement getEvents() method.
    }

    public function createEvent(string $calendarId, Event $event): Event
    {
        // https://learn.microsoft.com/en-us/graph/api/user-post-events?view=graph-rest-1.0&tabs=http

        // TODO: Implement createEvent() method.
    }

    public function updateEvent(string $calendarId, Event $event): Event
    {
        // https://learn.microsoft.com/en-us/graph/api/event-update?view=graph-rest-1.0&tabs=http

        // TODO: Implement updateEvent() method.
    }

    public function deleteEvent(string $calendarId, Event $event): void
    {
        // https://learn.microsoft.com/en-us/graph/api/event-delete?view=graph-rest-1.0&tabs=http

        // TODO: Implement deleteEvent() method.
    }

    public function syncEvents(string $calendarId, User $user): void
    {
        // TODO: Implement syncEvents() method.
    }
}
