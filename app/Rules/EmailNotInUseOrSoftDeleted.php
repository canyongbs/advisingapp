<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailNotInUseOrSoftDeleted implements ValidationRule
{
    protected $message;
    protected $currentUserId;

    public function __construct($currentUserId = null)
    {
        $this->currentUserId = $currentUserId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::withTrashed()->where('email', $value)->first();

        if ($user) {
            if ($this->currentUserId && $user->id === $this->currentUserId) {
                return; // Allow the current user to keep their email
            }

            if ($user->trashed()) {
                $fail('An archived user with this email address already exists. Please contact an administrator to restore this user or use a different email address.');
            } else {
                $fail("A user with this email address already exists. Please use a different email address or contact your administrator if you need to modify this user's account.");
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
