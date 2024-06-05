<?php

namespace AdvisingApp\Ai\Rules;

use Closure;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;

class RestrictSuperAdmin implements ValidationRule
{
    public string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($value as $id) {
            if (User::findOrFail($id)->hasRole('authorization.super_admin')) {
                if ($this->type === 'clone') {
                    $fail('Super admin users cannot have a thread shared with them.');
                } elseif ($this->type === 'email') {
                    $fail('Super admin users cannot have a thread emailed to them.');
                }
            }
        }
    }
}
