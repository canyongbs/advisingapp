<?php

namespace Assist\Alert\Enums;

use Filament\Support\Contracts\HasLabel;

enum AlertStatus: string implements HasLabel
{
    case Active = 'active';

    case Resolved = 'resolved';

    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function default(): AlertStatus
    {
        return AlertStatus::Active;
    }
}
