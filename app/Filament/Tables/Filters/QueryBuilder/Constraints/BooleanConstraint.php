<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EndsWithOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\IsFilledOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\StartsWithOperator;
use Closure;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class BooleanConstraint extends Constraint
{
    protected bool | Closure $isNullable = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-check-circle');

        $this->operators([
            IsTrueOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }

    public function nullable(bool | Closure $condition = true): static
    {
        $this->isNullable = $condition;

        return $this;
    }

    public function isNullable(): bool
    {
        return (bool) $this->evaluate($this->isNullable);
    }
}
