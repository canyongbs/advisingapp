<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class HasMaxOperator extends Operator
{
    public function getName(): string
    {
        return 'hasMax';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Has more than' : 'Has maximum';
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

    public function getSummary(): string
    {
        return $this->isInverse() ? "Has more than {$this->getSettings()['count']} {$this->getConstraint()->getLabel()}" : "Has maximum {$this->getSettings()['count']} {$this->getConstraint()->getLabel()}";
    }

    public function baseQuery(Builder $query): Builder
    {
        return $query->has($this->getConstraint()->getRelationshipName(), $this->isInverse() ? '>' : '<=', intval($this->getSettings()['count']));
    }
}
