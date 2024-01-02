<?php

namespace AdvisingApp\Authorization\Rules;

use Closure;
use AdvisingApp\Authorization\Enums\LicenseType;
use Illuminate\Contracts\Validation\ValidationRule;

class LicenseTypeUsageRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $licenseType = LicenseType::from($value);

        if (! $licenseType->hasAvailableLicenses()) {
            $fail("There are no available {$licenseType->getLabel()} licenses.");
        }
    }
}
