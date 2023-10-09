<?php

namespace App\Filament\Tables\Filters;

use App\Filament\Tables\Filters\QueryBuilder\Forms\Components\ConditionRepeater;
use Filament\Tables\Filters\BaseFilter;

class QueryBuilder extends BaseFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->form([
            ConditionRepeater::make('conditions'),
        ]);

        $this->columnSpanFull();
    }
}
