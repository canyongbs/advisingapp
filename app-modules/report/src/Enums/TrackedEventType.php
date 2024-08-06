<?php

namespace AdvisingApp\Report\Enums;

use Filament\Support\Contracts\HasLabel;

enum TrackedEventType: string implements HasLabel
{
    case AiExchange = 'ai-exchange';

    case AiThreadSaved = 'ai-thread-saved';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
