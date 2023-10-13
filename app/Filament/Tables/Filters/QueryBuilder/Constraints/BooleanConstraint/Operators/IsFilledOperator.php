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
        return $this->isInverse() ? "{$this->getConstraint()->getLabel()} is blank" : "{$this->getConstraint()->getLabel()} is filled";
    }

    public function query(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->{$this->isInverse() ? 'whereNull' : 'whereNotNull'}($qualifiedColumn)
                ->{$this->isInverse() ? 'where' : 'whereNot'}($qualifiedColumn, ''),
        );
    }
}
