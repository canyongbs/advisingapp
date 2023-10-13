<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class HasMinOperator extends Operator
{
    public function getName(): string
    {
        return 'hasMin';
    }

    public function getLabel(bool $isInverse): string
    {
        return $isInverse ? 'Has less than' : 'Has minimum';
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
        return $isInverse ? "Has less than {$settings['count']} {$constraint->getLabel()}" : "Has minimum {$settings['count']} {$constraint->getLabel()}";
    }

    public function applyToQueryForConstraint(Builder $query, Constraint $constraint, array $settings, bool $isInverse): Builder
    {
        return $query->has($constraint->getRelationshipName(), $isInverse ? '<' : '>=', intval($settings['count']));
    }
}
