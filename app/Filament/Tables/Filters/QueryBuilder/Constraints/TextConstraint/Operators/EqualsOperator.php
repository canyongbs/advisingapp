<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class EqualsOperator extends Operator
{
    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Does not equal' : 'Equals';
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('text')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "{$this->getConstraint()->getLabel()} does not equal {$this->getSettings()['text']}" : "{$this->getConstraint()->getLabel()} equals {$this->getSettings()['text']}";
    }

    public function query(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, 'ilike', trim($this->getSettings()['text']));
    }
}
