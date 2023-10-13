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
        return $this->isInverse() ? "{$this->getConstraint()->getLabel()} is false" : "{$this->getConstraint()->getLabel()} is true";
    }

    public function query(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($qualifiedColumn, ! $this->isInverse());
    }
}
