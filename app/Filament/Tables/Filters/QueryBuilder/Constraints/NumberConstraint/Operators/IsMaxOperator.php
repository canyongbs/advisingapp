<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

use function Filament\Support\format_number;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class IsMaxOperator extends Operator
{
    use Concerns\CanAggregateRelationships;

    public function getName(): string
    {
        return 'isMax';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.number.is_max.label.inverse' :
                'filament-tables::filters/query-builder.operators.number.is_max.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.number.is_max.summary.inverse' :
                'filament-tables::filters/query-builder.operators.number.is_max.summary.direct',
            [
                'attribute' => $this->getAttributeLabel(),
                'number' => format_number($this->getSettings()['number']),
            ],
        );
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('number')
                ->label(__('filament-tables::filters/query-builder.operators.number.form.number.label'))
                ->numeric()
                ->integer($this->getConstraint()->isInteger())
                ->required(),
            $this->getAggregateSelect(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($this->replaceQualifiedColumnWithQualifiedAggregateColumn($qualifiedColumn), $this->isInverse() ? '>' : '<=', floatval($this->getSettings()['number']));
    }
}
