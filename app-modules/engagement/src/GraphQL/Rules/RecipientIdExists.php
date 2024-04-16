<?php

namespace AdvisingApp\Engagement\GraphQL\Rules;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\Relation;

class RecipientIdExists implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = $this->data['input']['recipient_type'];

        /** @var ?Model $morph */
        $morph = Relation::getMorphedModel($type);

        if (! $morph) {
            $fail('The type must be either student or prospect.');
        } elseif ($morph::query()->whereKey($value)->doesntExist()) {
            $fail('The recipient does not exist.');
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
