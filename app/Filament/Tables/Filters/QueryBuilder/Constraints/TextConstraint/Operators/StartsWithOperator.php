<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\TextRule\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class StartsWithOperator extends Operator
{
    public function getName(): string
    {
        return 'startsWith';
    }

    public function getLabel(bool $isInverse): string
    {
        return $isInverse ? 'Does not start with' : 'Starts with';
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('substring')
                ->required(),
        ];
    }

    public function getSummary(string $attributeLabel, array $settings, bool $isInverse): string
    {
        return $isInverse ? "{$attributeLabel} does not start with {$settings['substring']}" : "{$attributeLabel} starts with {$settings['substring']}";
    }

    public function query(Builder $query, string $attribute, array $settings, bool $isInverse): Builder
    {
        return $query->{$isInverse ? 'whereNot' : 'where'}($attribute, 'ilike', "{$settings['substring']}%");
    }
}
