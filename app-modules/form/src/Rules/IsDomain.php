<?php

namespace Assist\Form\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IsDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/', $value)) {
            $fail('All entries must be valid domains.');
        }
    }
}
