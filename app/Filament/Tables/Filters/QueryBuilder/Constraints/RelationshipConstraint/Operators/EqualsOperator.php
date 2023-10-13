<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class EqualsOperator extends Operator
{
    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(bool $isInverse): string
    {
        return $isInverse ? 'Does not have' : 'Has';
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('count')
                ->integer()
                ->required()
                ->minValue(1),
        ];
    }

    public function getSummary(Constraint $constraint, array $settings, bool $isInverse): string
    {
        return $isInverse ? "Does not have {$settings['count']} {$constraint->getLabel()}" : "Has {$settings['count']} {$constraint->getLabel()}";
    }

    public function applyToQueryForConstraint(Builder $query, Constraint $constraint, array $settings, bool $isInverse): Builder
    {
        return $query->has($constraint->getRelationshipName(), $isInverse ? '!=' : '=', intval($settings['count']));
    }
}
