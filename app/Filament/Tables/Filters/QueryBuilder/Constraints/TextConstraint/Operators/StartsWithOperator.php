<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class StartsWithOperator extends Operator
{
    public function getName(): string
    {
        return 'startsWith';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Does not start with' : 'Starts with';
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
        return $this->isInverse() ? "{$this->getConstraint()->getLabel()} does not start with {$this->getSettings()['text']}" : "{$this->getConstraint()->getLabel()} starts with {$this->getSettings()['text']}";
    }

    public function query(Builder $query, string $qualifiedColumn): Builder
    {
        $text = trim($this->getSettings()['text']);

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, 'ilike', "{$text}%");
    }
}
