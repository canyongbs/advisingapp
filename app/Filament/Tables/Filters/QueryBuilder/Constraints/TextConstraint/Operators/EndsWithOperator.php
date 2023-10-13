<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class EndsWithOperator extends Operator
{
    public function getName(): string
    {
        return 'endsWith';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Does not end with' : 'Ends with';
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
        return $this->isInverse() ? "{$this->getConstraint()->getLabel()} does not end with {$this->getSettings()['text']}" : "{$this->getConstraint()->getLabel()} ends with {$this->getSettings()['text']}";
    }

    public function query(Builder $query, string $qualifiedColumn): Builder
    {
        $text = trim($this->getSettings()['text']);

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, 'ilike', "%{$text}");
    }
}
