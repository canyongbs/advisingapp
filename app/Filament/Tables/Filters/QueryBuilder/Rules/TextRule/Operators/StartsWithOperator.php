<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Rules\TextRule\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Rules\Operators\Operator;

class StartsWithOperator extends Operator
{
    public function getName(): string
    {
        return 'startsWith';
    }

    public function getLabel(): string
    {
        return 'Starts with';
    }

    public function getInverseLabel(): string
    {
        return 'Does not start with';
    }
}
