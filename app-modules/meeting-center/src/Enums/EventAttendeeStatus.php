<?php

namespace AdvisingApp\MeetingCenter\Enums;

enum EventAttendeeStatus: string
{
    case Invited = 'invited';

    case Pending = 'pending';

    case Attending = 'attending';

    case NotAttending = 'not_attending';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NotAttending => 'Not Attending',
            default => $this->name,
        };
    }
}
