<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\AttributeTextConstraint\Operators;

use Filament\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use libphonenumber\PhoneNumberUtil;

/**
 * Compares a code extracted from an E.164 phone number column, such as its country or area code.
 */
class PhoneNumberPartOperator extends AttributeTextOperator
{
    /**
     * PostgreSQL regular expression whose first capture group is the part to compare.
     */
    protected ?string $pattern = null;

    public function countryCode(): static
    {
        // Calling codes are prefix-free, so exactly one alternative can match an E.164 number.
        $this->pattern = '^\+(' . implode('|', PhoneNumberUtil::getInstance()->getSupportedCallingCodes()) . ')';

        return $this;
    }

    /**
     * Only North American (+1) numbers have a fixed-position area code; other numbers never match.
     */
    public function areaCode(): static
    {
        $this->pattern = '^\+1([2-9][0-9]{2})';

        return $this;
    }

    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    protected function applyTextOperator(Builder $query, Operator $textOperator): Builder
    {
        assert(filled($this->pattern));

        $digits = preg_replace('/\D/', '', $textOperator->getSettings()[static::TEXT_SETTING] ?? '');

        // A digit-free value would become a match-all pattern, so match nothing instead.
        if (blank($digits)) {
            return $query->whereRaw('false');
        }

        $pattern = match ($textOperator->getName()) {
            'contains' => "%{$digits}%",
            'startsWith' => "{$digits}%",
            'endsWith' => "%{$digits}",
            default => $digits,
        };

        $column = $query->getQuery()->getGrammar()->wrap($query->qualifyColumn($this->getColumn()));
        $comparison = $textOperator->isInverse() ? 'not like' : 'like';

        return $query->whereRaw("substring({$column} from ?) {$comparison} ?", [$this->pattern, $pattern]);
    }
}
