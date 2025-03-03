<?php

namespace App\Rules;

use AdvisingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Translation\PotentiallyTranslatedString;

class ExcludeSuperAdmin implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = Role::query();

        $query->when(
            ! auth()->user()->isSuperAdmin(),
            fn (Builder $query) => $query->where('name', '!=', Authenticatable::SUPER_ADMIN_ROLE)
        );

        if (! $query->where('id', $value)->exists()) {
            $fail('The selected role is not allowed.');
        }
    }
}
