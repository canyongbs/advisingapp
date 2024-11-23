<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TagType: string implements HasLabel
{
    case Student = 'Student';
    case Prospect = 'Prospect';

    public function getLabel(): string
    {
        return match ($this) {
            TagType::Student => 'Student',
            TagType::Prospect => 'Prospect',
        };
    }
}
