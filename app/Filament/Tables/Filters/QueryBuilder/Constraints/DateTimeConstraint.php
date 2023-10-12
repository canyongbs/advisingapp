<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;

class DateTimeConstraint extends Constraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-calendar-days');
    }
}
