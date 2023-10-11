<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Rules\TextRule\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Rules\Operators\Operator;

class ContainsOperator extends Operator
{
    public function getName(): string
    {
        return 'contains';
    }

    public function getLabel(): string
    {
        return 'Contains';
    }

    public function getInverseLabel(): string
    {
        return 'Does not contain';
    }
}
