<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Rules;

use App\Filament\Tables\Filters\QueryBuilder\Rules\Rule;

class NumericRule extends Rule
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-calculator');
    }
}
