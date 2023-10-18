<?php

namespace Assist\Prospect\Enums;

use Filament\Support\Contracts\HasLabel;

enum SystemProspectClassification: string implements HasLabel
{
    case New = 'new';

    case Assigned = 'assigned';

    case InProgress = 'in_progress';

    case Converted = 'converted';

    case Recycled = 'recycled';

    case Dead = 'dead';

    case Custom = 'custom';

    public function getLabel(): ?string
    {
        return match ($this) {
            SystemProspectClassification::InProgress => 'In Progress',
            default => $this->name,
        };
    }
}
