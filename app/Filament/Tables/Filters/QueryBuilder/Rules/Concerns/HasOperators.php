<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Rules\Concerns;

use App\Filament\Tables\Filters\QueryBuilder\Rules\Operators\Operator;

trait HasOperators
{
    /** @var array<Operator> */
    protected array $operators = [];

    /**
     * @param array<class-string<Operator> | Operator> $operators
     */
    public function operators(array $operators): static
    {
        foreach ($operators as $operator) {
            if (is_string($operator)) {
                $operator = app($operator);
            }

            $this->operators[$operator->getName()] = $operator;
        }

        $this->operators = array_map(
            fn (string | Operator $operator): Operator => is_string($operator) ? app($operator) : $operator,
            $operators,
        );

        return $this;
    }

    /**
     * @return array<Operator>
     */
    public function getOperators(): array
    {
        return $this->evaluate($this->operators);
    }
}
