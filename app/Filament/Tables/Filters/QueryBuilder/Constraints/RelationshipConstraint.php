<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator;
use Closure;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RelationshipConstraint extends Constraint
{
    protected bool | Closure $isMultiple = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-arrows-pointing-out');

        $this->operators([
            HasMinOperator::make()->visible(fn (): bool => $this->isMultiple()),
            HasMaxOperator::make()->visible(fn (): bool => $this->isMultiple()),
            IsEmptyOperator::class,
            EqualsOperator::make()->visible(fn (): bool => $this->isMultiple()),
        ]);
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }
}
