<?php

namespace App\Filament\Tables\Filters;

use App\Filament\Tables\Filters\QueryBuilder\Concerns\HasRules;
use App\Filament\Tables\Filters\QueryBuilder\Forms\Components\RuleRepeater;
use App\Filament\Tables\Filters\QueryBuilder\Rules\Rule;
use Filament\Tables\Filters\BaseFilter;

class QueryBuilder extends BaseFilter
{
    use HasRules;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form(fn (QueryBuilder $filter): array => [
            RuleRepeater::make('rules')
                ->rules($filter->getRules()),
        ]);

        $this->columnSpanFull();
    }
}
