<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint\Operators\IsOperator;

class ExistingValuesSelectConstraint extends SelectConstraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->operators([
            IsOperator::make()
                ->modifyQueryUsing(function (IsOperator $operator, Builder $query, string $qualifiedColumn, array $settings): Builder {
                    $value = $settings[$this->isMultiple() ? 'values' : 'value'];
            
                    if (is_array($value)) {
                        return $query->{$operator->isInverse() ? 'whereNotIn' : 'whereIn'}(new Expression("lower({$qualifiedColumn})"), $value);
                    }
            
                    return $query->{$operator->isInverse() ? 'whereNot' : 'where'}(new Expression("lower({$qualifiedColumn})"), $value);
                }),
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);

        $this->options(fn (SelectConstraint $constraint): array => $constraint->getFilter()->getTable()->getQuery()
            ->distinct(new Expression("lower({$constraint->getAttribute()})"))
            ->pluck($constraint->getAttribute())
            ->mapWithKeys(fn (string $option): array => [Str::lower($option) => Str::title($option)])
            ->all());
    }
}
