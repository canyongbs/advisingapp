<?php

namespace AdvisingApp\Notifications\Rules;

use Closure;
use AdvisingApp\Notifications\Models\Subscription;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueSubscriptionRule implements DataAwareRule, ValidationRule
{
    protected $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            Subscription::query()
                ->where('subscribable_id', $this->data['subscribable_id'])
                ->where('subscribable_type', $this->data['subscribable_type'])
                ->where('user_id', $value)
                ->exists()
        ) {
            $fail('The user is already subscribed.');
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
