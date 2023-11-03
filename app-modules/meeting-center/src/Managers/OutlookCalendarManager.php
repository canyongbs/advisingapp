<?php

namespace Assist\MeetingCenter\Managers;

use DateTime;
use Assist\MeetingCenter\Models\Calendar;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\Managers\Contracts\CalendarInterface;

class OutlookCalendarManager implements CalendarInterface
{
    /**
     * @return array<string, string>
     */
    public function getCalendars(Calendar $calendar): array
    {
        // TODO: Implement getCalendars() method.

        return [];
    }

    // https://github.com/microsoftgraph/msgraph-sample-phpapp/tree/main
    // https://github.com/microsoftgraph/msgraph-sdk-php
    // https://learn.microsoft.com/en-us/graph/api/resources/webhooks?view=graph-rest-1.0

    public function getEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): array
    {
        // https://learn.microsoft.com/en-us/graph/api/user-list-calendars?view=graph-rest-1.0&tabs=http
        // https://learn.microsoft.com/en-us/graph/api/user-list-events?view=graph-rest-1.0&tabs=http

        // TODO: Implement getEvents() method.

        return [];
    }

    public function createEvent(CalendarEvent $event): void
    {
        // https://learn.microsoft.com/en-us/graph/api/user-post-events?view=graph-rest-1.0&tabs=http

        // TODO: Implement createEvent() method.
    }

    public function updateEvent(CalendarEvent $event): void
    {
        // https://learn.microsoft.com/en-us/graph/api/event-update?view=graph-rest-1.0&tabs=http

        // TODO: Implement updateEvent() method.
    }

    public function deleteEvent(CalendarEvent $event): void
    {
        // https://learn.microsoft.com/en-us/graph/api/event-delete?view=graph-rest-1.0&tabs=http

        // TODO: Implement deleteEvent() method.
    }

    public function syncEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): void
    {
        // TODO: Implement syncEvents() method.
    }
}
