<?php

namespace Assist\Form\Enums;

use Illuminate\Support\Str;
use Filament\Support\Contracts\HasLabel;

enum Rounding: string implements HasLabel
{
    case None = 'none';

    case Small = 'sm';

    case Medium = 'md';

    case Large = 'lg';

    case Full = 'full';

    public function getLabel(): ?string
    {
        return Str::title($this->name);
    }

    public static function getDefault(): self
    {
        return self::Medium;
    }
}
