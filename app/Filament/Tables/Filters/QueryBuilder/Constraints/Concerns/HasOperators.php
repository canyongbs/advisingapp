<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\Concerns;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

trait HasOperators
{
    /** @var array<Operator> */
    protected array $operators = [];

    /**
     * @param  array<class-string<Operator> | Operator>  $operators
     */
    public function unshiftOperators(array $operators): static
    {
        foreach ($operators as $operator) {
            if (is_string($operator)) {
                $operator = $operator::make();
            }

            $operatorName = $operator->getName();

            if (array_key_exists($operatorName, $this->operators)) {
                unset($this->operators[$operatorName]);
            }

            $this->operators = [
                $operatorName => $operator,
                ...$this->operators,
            ];
        }

        return $this;
    }

    /**
     * @param  array<class-string<Operator> | Operator>  $operators
     */
    public function operators(array $operators): static
    {
        foreach ($operators as $operator) {
            if (is_string($operator)) {
                $operator = $operator::make();
            }

            $this->operators[$operator->getName()] = $operator;
        }

        return $this;
    }

    /**
     * @return array<Operator>
     */
    public function getOperators(): array
    {
        return array_filter(
            $this->operators,
            fn (Operator $operator): bool => $operator->isVisible(),
        );
    }

    public function getOperator(string $name): ?Operator
    {
        return $this->getOperators()[$name] ?? null;
    }
}
