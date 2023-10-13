<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators;

use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class IsFilledOperator extends Operator
{
    public function getName(): string
    {
        return 'isFilled';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is blank' : 'Is filled';
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "{$this->getconstraint()->getAttributeLabel()} is blank" : "{$this->getconstraint()->getAttributeLabel()} is filled";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->{$this->isInverse() ? 'whereNull' : 'whereNotNull'}($qualifiedColumn)
                ->{$this->isInverse() ? 'where' : 'whereNot'}($qualifiedColumn, ''),
        );
    }
}
