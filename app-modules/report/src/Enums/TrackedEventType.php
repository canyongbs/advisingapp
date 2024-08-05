<?php

namespace AdvisingApp\Report\Enums;

use Filament\Support\Contracts\HasLabel;

enum TrackedEventType: string implements HasLabel
{
    case AiUtterance = 'ai-utterance';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
