<?php

namespace AdvisingApp\StudentDataModel\Enums;

use Filament\Support\Contracts\HasLabel;

enum ActionCenterTab: string implements HasLabel
{
    case All = 'all';

    case Subscribed = 'subscribed';

    case CareTeam = 'care_team';

    public function getLabel(): string
    {
        return match ($this) {
            self::All => 'All',
            self::Subscribed => 'Subscribed',
            self::CareTeam => 'Care Team',
        };
    }
}
