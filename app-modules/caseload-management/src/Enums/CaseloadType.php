<?php

namespace Assist\CaseloadManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum CaseloadType: string implements HasLabel
{
    case Dynamic = 'dynamic';

    case Static = 'static';

    public function getLabel(): ?string
    {
        return match ($this) {
            CaseloadType::Static => 'Static (Coming Soon!)',
            default => $this->name,
        };
    }

    public function disabled(): bool
    {
        return match ($this) {
            CaseloadType::Static => true,
            default => false
        };
    }

    public static function default(): CaseloadType
    {
        return CaseloadType::Dynamic;
    }
}
