<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Rules\TextRule\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Rules\Operators\Operator;

class EndsWithOperator extends Operator
{
    public function getName(): string
    {
        return 'endsWith';
    }

    public function getLabel(): string
    {
        return 'Ends with';
    }

    public function getInverseLabel(): string
    {
        return 'Does not end with';
    }
}
