<?php

namespace Assist\Prospect\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProspectStatusColorOptions: string implements HasLabel
{
    case Success = 'success';

    case Danger = 'danger';

    case Warning = 'warning';

    case Info = 'info';

    case Primary = 'primary';

    case Gray = 'gray';

    public function getLabel(): string
    {
        return $this->value;
    }
}
