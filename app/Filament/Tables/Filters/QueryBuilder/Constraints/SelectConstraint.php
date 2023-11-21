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

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use Closure;
use Filament\Tables\Filters\Concerns\HasOptions;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint\Operators\IsOperator;

class SelectConstraint extends Constraint
{
    use HasOptions;

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isNative = true;

    protected bool | Closure $isSearchable = false;

    protected int | Closure $optionsLimit = 50;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-chevron-up-down');

        $this->operators([
            IsOperator::class,
            IsFilledOperator::class,
        ]);
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function native(bool | Closure $condition = true): static
    {
        $this->isNative = $condition;

        return $this;
    }

    public function isNative(): bool
    {
        return (bool) $this->evaluate($this->isNative);
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function getOptionLabelFromRecordUsingCallback(): ?Closure
    {
        return $this->getOptionLabelFromRecordUsing;
    }
}
