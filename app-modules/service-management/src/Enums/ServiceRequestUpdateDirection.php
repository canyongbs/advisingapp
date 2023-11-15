<?php

namespace Assist\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceRequestUpdateDirection: string implements HasLabel
{
    case Inbound = 'inbound';

    case Outbound = 'outbound';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return match ($this) {
            ServiceRequestUpdateDirection::Inbound => 'heroicon-o-arrow-down-tray',
            ServiceRequestUpdateDirection::Outbound => 'heroicon-o-arrow-up-tray',
        };
    }

    public static function default(): ServiceRequestUpdateDirection
    {
        return ServiceRequestUpdateDirection::Outbound;
    }
}
