<?php

namespace Assist\Assistant\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssistantChatShareWith: string implements HasLabel
{
    case User = 'user';
    case Team = 'team';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function default(): AssistantChatShareWith
    {
        return AssistantChatShareWith::User;
    }
}
