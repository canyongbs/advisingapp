<?php

namespace Assist\MeetingCenter;

use Assist\MeetingCenter\Models\Event;

abstract class CalendarProvider
{
    abstract public function getEvents(): array;

    abstract public function createEvent(Event $event);

    abstract public function deleteEvent(Event $event);

    abstract public function updateEvent(Event $event);
}
