<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMaxOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMinOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\EqualsOperator;

class NumberConstraint extends Constraint
{
    protected array $existingAggregateAliases = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-variable');

        $this->operators([
            IsMinOperator::class,
            IsMaxOperator::class,
            EqualsOperator::class,
            IsFilledOperator::class,
        ]);
    }

    public function reportAggregateAlias(string $alias): static
    {
        $this->existingAggregateAliases[$alias] = $alias;

        return $this;
    }

    public function isExistingAggregateAlias(string $alias): bool
    {
        return array_key_exists($alias, $this->existingAggregateAliases);
    }
}
