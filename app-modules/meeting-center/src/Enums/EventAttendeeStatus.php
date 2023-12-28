<?php

namespace AdvisingApp\MeetingCenter\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventAttendeeStatus: string implements HasColor, HasLabel
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

    public function getColor(): string
    {
        return match ($this) {
            self::Invited => 'info',
            self::Pending => 'warning',
            self::Attending => 'success',
            self::NotAttending => 'danger',
        };
    }
}
