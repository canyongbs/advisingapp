<?php

namespace Assist\MeetingCenter;

use Assist\MeetingCenter\Models\Event;

abstract class CalendarProvider
{
    abstract public static function type(): string;

    abstract public function getEvents(string $calendarId): array;

    abstract public function createEvent(string $calendarId, Event $event): Event;

    abstract public function updateEvent(string $calendarId, Event $event): void;

    abstract public function deleteEvent(string $calendarId, Event $event): void;
}
