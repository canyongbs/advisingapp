<?php

namespace Assist\Assistant\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssistantChatShareVia: string implements HasLabel
{
    case Email = 'email';
    case Internal = 'internal';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function default(): AssistantChatShareVia
    {
        return AssistantChatShareVia::Internal;
    }
}
