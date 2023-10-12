<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators;

use Illuminate\Database\Eloquent\Builder;

abstract class Operator
{
    abstract public function getName(): string;

    abstract public function getLabel(bool $isInverse): string;

    abstract public function getSummary(string $attributeLabel, array $settings, bool $isInverse): string;

    public function getFormSchema(): array
    {
        return [];
    }

    abstract public function query(Builder $query, string $attribute, array $settings, bool $isInverse): Builder;
}
