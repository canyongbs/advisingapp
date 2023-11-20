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
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.boolean.is_true.label.inverse' :
                'filament-tables::filters/query-builder.operators.boolean.is_true.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.boolean.is_true.summary.inverse' :
                'filament-tables::filters/query-builder.operators.boolean.is_true.summary.direct',
            ['attribute' => $this->getConstraint()->getAttributeLabel()],
        );
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($qualifiedColumn, ! $this->isInverse());
    }
}
