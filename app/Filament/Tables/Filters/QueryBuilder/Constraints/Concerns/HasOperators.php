<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\Concerns;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

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

        return $this;
    }

    /**
     * @return array<Operator>
     */
    public function getOperators(): array
    {
        return $this->evaluate($this->operators);
    }

    public function getOperator(string $name): ?Operator
    {
        return $this->getOperators()[$name] ?? null;
    }
}
