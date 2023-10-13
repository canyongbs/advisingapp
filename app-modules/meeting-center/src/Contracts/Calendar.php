<?php

namespace Assist\MeetingCenter\Contracts;

use DateTime;
use App\Models\User;
use Assist\MeetingCenter\Models\Event;

interface Calendar
{
    public static function type(): string;

    public function getEvents(string $calendarId, ?Datetime $start = null, ?Datetime $end = null): array;

    public function createEvent(string $calendarId, Event $event): Event;

    public function updateEvent(string $calendarId, Event $event): Event;

    public function deleteEvent(string $calendarId, Event $event): void;

    public function syncEvents(string $calendarId, User $user): void;
}
