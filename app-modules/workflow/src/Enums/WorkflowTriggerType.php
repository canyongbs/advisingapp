<?php

namespace AdvisingApp\Workflow\Enums;

use Filament\Support\Contracts\HasLabel;

enum WorkflowTriggerType: string implements HasLabel
{
    case EventBased = 'event_based';
    case TimeBased = 'time_based';

    public function getLabel(): string
    {
        return $this->name;
    }
}