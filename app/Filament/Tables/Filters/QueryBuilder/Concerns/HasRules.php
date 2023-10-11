<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Concerns;

use App\Filament\Tables\Filters\QueryBuilder\Rules\Rule;

trait HasRules
{
    /** @var array<Rule> */
    protected array $rules = [];

    /**
     * @param array<Rule> $rules
     */
    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return array<Rule>
     */
    public function getRules(): array
    {
        return $this->evaluate($this->rules);
    }
}
