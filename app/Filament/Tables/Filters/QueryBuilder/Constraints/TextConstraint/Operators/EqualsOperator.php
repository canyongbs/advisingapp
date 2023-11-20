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

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use Illuminate\Support\Str;
use Illuminate\Database\Connection;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class EqualsOperator extends Operator
{
    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.text.equals.label.inverse' :
                'filament-tables::filters/query-builder.operators.text.equals.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.text.equals.summary.inverse' :
                'filament-tables::filters/query-builder.operators.text.equals.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'text' => $this->getSettings()['text'],
            ],
        );
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('text')
                ->label(__('filament-tables::filters/query-builder.operators.text.form.text.label'))
                ->required()
                ->columnSpanFull(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $text = trim($this->getSettings()['text']);

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        if ($databaseConnection->getDriverName() === 'pgsql') {
            $qualifiedColumn = new Expression("lower({$qualifiedColumn}::text)");
            $text = Str::lower($text);
        }

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, 'like', $text);
    }
}
