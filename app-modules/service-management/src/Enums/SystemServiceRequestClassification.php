<?php

namespace Assist\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum SystemServiceRequestClassification: string implements HasLabel
{
    case Open = 'open';

    case InProgress = 'in_progress';

    case Closed = 'closed';

    case Custom = 'custom';

    public function getLabel(): ?string
    {
        return match ($this) {
            SystemServiceRequestClassification::InProgress => 'In Progress',
            default => $this->name,
        };
    }
}
