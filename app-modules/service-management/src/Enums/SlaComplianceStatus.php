<?php

namespace AdvisingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SlaComplianceStatus implements HasColor, HasIcon, HasLabel
{
    case Compliant;

    case NonCompliant;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Compliant => 'Within SLA',
            self::NonCompliant => 'Outside of SLA',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Compliant => 'success',
            self::NonCompliant => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Compliant => 'heroicon-m-check-circle',
            self::NonCompliant => 'heroicon-m-x-circle',
        };
    }
}
