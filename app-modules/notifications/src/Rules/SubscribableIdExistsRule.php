<?php

namespace AdvisingApp\Notifications\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\Relation;

class SubscribableIdExistsRule implements DataAwareRule, ValidationRule
{
    protected $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            ! Relation::getMorphedModel($this->data['subscribable_type'])::query()
                ->whereKey($value)
                ->exists()
        ) {
            $fail('The subscribable does not exist.');
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
