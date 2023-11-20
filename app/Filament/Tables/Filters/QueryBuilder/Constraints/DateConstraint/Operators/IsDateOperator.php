<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators;

use Illuminate\Support\Carbon;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class IsDateOperator extends Operator
{
    public function getName(): string
    {
        return 'isDate';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.date.is_date.label.inverse' :
                'filament-tables::filters/query-builder.operators.date.is_date.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.date.is_date.summary.inverse' :
                'filament-tables::filters/query-builder.operators.date.is_date.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'date' => Carbon::parse($this->getSettings()['date'])->toFormattedDateString(),
            ],
        );
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            DatePicker::make('date')
                ->label(__('filament-tables::filters/query-builder.operators.date.form.date.label'))
                ->required(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->whereDate($qualifiedColumn, $this->isInverse() ? '!=' : '=', $this->getSettings()['date']);
    }
}
