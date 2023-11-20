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
            CaseloadType::Static => 'Static',
            default => $this->name,
        };
    }

    public static function default(): CaseloadType
    {
        return CaseloadType::Dynamic;
    }

    public static function tryFromCaseOrValue(CaseloadType | string $value): ?CaseloadType
    {
        if ($value instanceof CaseloadType) {
            return $value;
        }

        return static::tryFrom($value);
    }
}
