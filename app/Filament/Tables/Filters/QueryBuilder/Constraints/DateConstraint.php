<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsAfterOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsBeforeOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsDateOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsMonthOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsYearOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;

class DateConstraint extends Constraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-calendar');

        $this->operators([
            IsAfterOperator::class,
            IsBeforeOperator::class,
            IsDateOperator::class,
            IsMonthOperator::class,
            IsYearOperator::class,
            IsFilledOperator::class,
        ]);
    }
}
