<?php

namespace Assist\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

// TODO This might belong in a more generalized space so we can re-use this across modules
enum ColumnColorOptions: string implements HasLabel
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
