<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class IsEmptyOperator extends Operator
{
    public function getName(): string
    {
        return 'isEmpty';
    }

    public function getLabel(bool $isInverse): string
    {
        return $isInverse ? 'Is not empty' : 'Is empty';
    }

    public function getSummary(Constraint $constraint, array $settings, bool $isInverse): string
    {
        return $isInverse ? "{$constraint->getLabel()} is not empty" : "{$constraint->getLabel()} is empty";
    }

    public function applyToQueryForConstraint(Builder $query, Constraint $constraint, array $settings, bool $isInverse): Builder
    {
        return $query->{$isInverse ? 'has' : 'doesntHave'}($constraint->getRelationshipName());
    }
}
