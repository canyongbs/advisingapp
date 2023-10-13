<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
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
            TextInput::make('text')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public function getSummary(Constraint $constraint, array $settings, bool $isInverse): string
    {
        return $isInverse ? "{$constraint->getLabel()} does not start with {$settings['text']}" : "{$constraint->getLabel()} starts with {$settings['text']}";
    }

    public function query(Builder $query, string $attribute, array $settings, bool $isInverse): Builder
    {
        $text = trim($settings['text']);

        return $query->{$isInverse ? 'whereNot' : 'where'}($attribute, 'ilike', "{$text}%");
    }
}
