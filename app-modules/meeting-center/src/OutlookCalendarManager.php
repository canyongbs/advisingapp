<?php

namespace Assist\MeetingCenter;

use DateTime;
use App\Models\User;
use Assist\MeetingCenter\Models\Calendar;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\Contracts\CalendarInterface;

class OutlookCalendarManager implements CalendarInterface
{
    // https://github.com/microsoftgraph/msgraph-sample-phpapp/tree/main
    // https://github.com/microsoftgraph/msgraph-sdk-php
    // https://learn.microsoft.com/en-us/graph/api/resources/webhooks?view=graph-rest-1.0

    public static function type(): string
    {
        return 'outlook';
    }

    public function getEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null): array
    {
        // https://learn.microsoft.com/en-us/graph/api/user-list-calendars?view=graph-rest-1.0&tabs=http
        // https://learn.microsoft.com/en-us/graph/api/user-list-events?view=graph-rest-1.0&tabs=http

        // TODO: Implement getEvents() method.

        return [];
    }

    public function createEvent(CalendarEvent $event): CalendarEvent
    {
        // https://learn.microsoft.com/en-us/graph/api/user-post-events?view=graph-rest-1.0&tabs=http

        // TODO: Implement createEvent() method.

        return $event;
    }

    public function updateEvent(CalendarEvent $event): CalendarEvent
    {
        // https://learn.microsoft.com/en-us/graph/api/event-update?view=graph-rest-1.0&tabs=http

        // TODO: Implement updateEvent() method.

        return $event;
    }

    public function deleteEvent(CalendarEvent $event): void
    {
        // https://learn.microsoft.com/en-us/graph/api/event-delete?view=graph-rest-1.0&tabs=http

        // TODO: Implement deleteEvent() method.
    }

    public function syncEvents(Calendar $calendar): void
    {
        // TODO: Implement syncEvents() method.
    }
}
