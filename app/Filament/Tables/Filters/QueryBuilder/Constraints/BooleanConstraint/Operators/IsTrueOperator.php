<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators;

use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class IsTrueOperator extends Operator
{
    public function getName(): string
    {
        return 'isTrue';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is false' : 'Is true';
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "{$this->getConstraint()->getAttributeLabel()} is false" : "{$this->getConstraint()->getAttributeLabel()} is true";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($qualifiedColumn, ! $this->isInverse());
    }
}
