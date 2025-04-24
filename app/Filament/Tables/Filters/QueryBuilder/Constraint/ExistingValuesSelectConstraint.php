<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint\Operators\IsOperator;

class ExistingValuesSelectConstraint extends SelectConstraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->operators([
            IsOperator::make()
                ->modifyQueryUsing(function (IsOperator $operator, Builder $query, string $qualifiedColumn, array $settings): Builder {
                    $value = $settings[$this->isMultiple() ? 'values' : 'value'];
            
                    if (is_array($value)) {
                        return $query->{$operator->isInverse() ? 'whereNotIn' : 'whereIn'}(new Expression("lower({$qualifiedColumn})"), $value);
                    }
            
                    return $query->{$operator->isInverse() ? 'whereNot' : 'where'}(new Expression("lower({$qualifiedColumn})"), $value);
                }),
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);

        $this->options(fn (SelectConstraint $constraint): array => $constraint->getFilter()->getTable()->getQuery()
            ->distinct(new Expression("lower({$constraint->getAttribute()})"))
            ->pluck($constraint->getAttribute())
            ->mapWithKeys(fn (string $option): array => [Str::lower($option) => Str::title($option)])
            ->all());
    }
}
