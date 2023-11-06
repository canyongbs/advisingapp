<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EndsWithOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\StartsWithOperator;

class TextConstraint extends Constraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-language');

        $this->operators([
            ContainsOperator::class,
            StartsWithOperator::class,
            EndsWithOperator::class,
            EqualsOperator::class,
            IsFilledOperator::class,
        ]);
    }
}
