<?php

namespace Assist\MeetingCenter\Managers\Contracts;

use DateTime;
use Assist\MeetingCenter\Models\Calendar;
use Assist\MeetingCenter\Models\CalendarEvent;

interface CalendarInterface
{
    public function getEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): array;

    public function createEvent(CalendarEvent $event): void;

    public function updateEvent(CalendarEvent $event): void;

    public function deleteEvent(CalendarEvent $event): void;

    public function syncEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): void;
}
