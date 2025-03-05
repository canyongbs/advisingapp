<?php

namespace App\Rules;

use AdvisingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ExcludeSuperAdmin implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $role = Role::find($value);

        if (! $role) {
            $fail('The selected role does not exist.');

            return;
        }

        if (! auth()->user()->isSuperAdmin() && $role->name === Authenticatable::SUPER_ADMIN_ROLE) {
            $fail('You are not allowed to select the Super Admin role.');
        }
    }
}
