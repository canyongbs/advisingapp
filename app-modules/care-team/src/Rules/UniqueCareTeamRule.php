<?php

namespace AdvisingApp\CareTeam\Rules;

use Closure;
use AdvisingApp\CareTeam\Models\CareTeam;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCareTeamRule implements DataAwareRule, ValidationRule
{
    protected $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            CareTeam::query()
                ->where('educatable_id', $this->data['educatable_id'])
                ->where('educatable_type', $this->data['educatable_type'])
                ->where('user_id', $value)
                ->exists()
        ) {
            $fail('The user is already on the care team.');
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
