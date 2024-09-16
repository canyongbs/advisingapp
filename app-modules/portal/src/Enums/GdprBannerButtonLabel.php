<?php

namespace AdvisingApp\Portal\Enums;

use Filament\Support\Contracts\HasLabel;

enum GdprBannerButtonLabel: string implements HasLabel
{
    case Agree = 'Agree';

    case AllowCookies = 'Allow Cookies';

    case IUnderstand = 'I Understand';

    case Continue = 'Continue';

    public function getLabel(): string
    {
        return $this->value;
    }
}
