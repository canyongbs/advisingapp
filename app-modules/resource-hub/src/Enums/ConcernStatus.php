<?php

namespace AdvisingApp\ResourceHub\Enums;

use Filament\Support\Contracts\HasLabel;

enum ConcernStatus: string implements HasLabel
{
    case New = 'new';

    case Resolved = 'resolved';

    case Archived = 'archived';

    public function getLabel(): string
    {
        return $this->name;
    }
}
