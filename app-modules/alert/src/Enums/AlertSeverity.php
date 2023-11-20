<?php

namespace Assist\Alert\Enums;

use Filament\Support\Contracts\HasLabel;

enum AlertSeverity: string implements HasLabel
{
    case Low = 'low';

    case Medium = 'medium';

    case High = 'high';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function default(): AlertSeverity
    {
        return AlertSeverity::Low;
    }
}
