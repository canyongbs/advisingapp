<?php

namespace Assist\MeetingCenter\Contracts;

use DateTime;
use Assist\MeetingCenter\Models\Calendar;
use Assist\MeetingCenter\Models\CalendarEvent;

interface CalendarInterface
{
    public static function type(): string;

    public function getEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null): array;

    public function createEvent(CalendarEvent $event): CalendarEvent;

    public function updateEvent(CalendarEvent $event): CalendarEvent;

    public function deleteEvent(CalendarEvent $event): void;

    public function syncEvents(Calendar $calendar): void;
}
