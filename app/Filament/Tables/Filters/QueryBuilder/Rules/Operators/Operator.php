<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Rules\Operators;

abstract class Operator
{
    abstract public function getName(): string;

    abstract public function getLabel(): string;

    abstract public function getInverseLabel(): string;
}
