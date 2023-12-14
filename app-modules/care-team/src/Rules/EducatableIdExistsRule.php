<?php

namespace AdvisingApp\CareTeam\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\Relation;

class EducatableIdExistsRule implements DataAwareRule, ValidationRule
{
    protected $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            ! Relation::getMorphedModel($this->data['educatable_type'])::query()
                ->whereKey($value)
                ->exists()
        ) {
            $fail('The educatable does not exist.');
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
