<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use Closure;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperator;

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

    public function getAttributeLabel(): string
    {
        return $this->evaluate($this->attributeLabel) ?? str($this->getName())
            ->kebab()
            ->replace('-', ' ');
    }
}
