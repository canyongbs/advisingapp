<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators;

use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class IsFilledOperator extends Operator
{
    public function getName(): string
    {
        return 'isFilled';
    }

    public function getLabel(bool $isInverse): string
    {
        return $isInverse ? 'Is blank' : 'Is filled';
    }

    public function getSummary(Constraint $constraint, array $settings, bool $isInverse): string
    {
        return $isInverse ? "{$constraint->getLabel()} is blank" : "{$constraint->getLabel()} is filled";
    }

    public function query(Builder $query, string $attribute, array $settings, bool $isInverse): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->{$isInverse ? 'whereNull' : 'whereNotNull'}($attribute)
                ->{$isInverse ? 'where' : 'whereNot'}($attribute, ''),
        );
    }
}
