<?php

namespace Assist\MeetingCenter\Enums;

use Filament\Support\Contracts\HasLabel;

enum CalendarProvider: string implements HasLabel
{
    case Google = 'google';
    case Outlook = 'outlook';

    public function getLabel(): ?string
    {
        return str($this->name)->title();
    }
}
