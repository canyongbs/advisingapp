<?php

namespace AdvisingApp\Ai\Enums;

use Filament\Support\Contracts\HasLabel;

enum QnaAdvisorReportTableTab: string implements HasLabel
{
    case student = 'all';

    case prospect = 'subscribed';

    case unauthenticated = 'care_team';

    public function getLabel(): string
    {
        return match ($this) {
            self::student => 'Students',
            self::prospect => 'Prospects',
            self::unauthenticated => 'Unauthenticated',
        };
    }
}
