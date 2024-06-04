<?php

namespace AdvisingApp\Ai\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RestrictSuperAdmin implements ValidationRule
{
    public $type;

    public function __construct($type)
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
                if($this->type === 'clone') {
                    $fail('Super admin users cannot have a thread shared with them.');
                }
                else if($this->type === 'email') {
                    $fail('Super admin users cannot have a thread emailed to them.');
                }
            }
        }
    }
}
