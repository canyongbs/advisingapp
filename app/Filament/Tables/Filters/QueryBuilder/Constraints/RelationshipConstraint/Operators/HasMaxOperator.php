<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class HasMaxOperator extends Operator
{
    public function getName(): string
    {
        return 'hasMax';
    }

    public function getLabel(bool $isInverse): string
    {
        return $isInverse ? 'Has more than' : 'Has maximum';
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
        return $isInverse ? "Has more than {$settings['count']} {$constraint->getLabel()}" : "Has maximum {$settings['count']} {$constraint->getLabel()}";
    }

    public function applyToQueryForConstraint(Builder $query, Constraint $constraint, array $settings, bool $isInverse): Builder
    {
        return $query->has($constraint->getRelationshipName(), $isInverse ? '>' : '<=', intval($settings['count']));
    }
}
