<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CareTeamRoleType: string implements HasLabel
{
    case Student = 'student';
    case Prospect = 'prospect';

    public function getLabel(): string
    {
        return $this->name;
    }
}
