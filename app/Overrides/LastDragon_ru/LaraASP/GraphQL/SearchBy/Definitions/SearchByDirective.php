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

namespace App\Overrides\LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions;

use App\GraphQL\Directives\CanUseInQueryDirective;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\Context as ContextContract;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\Operator;
use LastDragon_ru\LaraASP\GraphQL\Builder\Exceptions\Client\ConditionEmpty;
use LastDragon_ru\LaraASP\GraphQL\Builder\Exceptions\Client\ConditionTooManyOperators;
use LastDragon_ru\LaraASP\GraphQL\Builder\Field;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions\SearchByDirective as BaseSearchByDirective;
use LastDragon_ru\LaraASP\GraphQL\Utils\ArgumentFactory;
use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;

class SearchByDirective extends BaseSearchByDirective
{
    /**
     * @template T of object
     *
     * @param T $builder
     *
     * @return T
     */
    protected function call(
        object $builder,
        Field $field,
        ArgumentSet $operator,
        ContextContract $context,
    ): object {
        // Arguments?
        if (count($operator->arguments) > 1) {
            throw new ConditionTooManyOperators(
                ArgumentFactory::getArgumentsNames($operator),
            );
        }

        // Operator & Value
        $op = null;
        $value = null;

        foreach ($operator->arguments as $name => $argument) {
            $operators = [];

            foreach ($argument->directives as $directive) {
                if ($directive instanceof CanUseInQueryDirective) {
                    $directive->authorize($builder);
                }

                if ($directive instanceof Operator) {
                    $operators[] = $directive;
                }
            }

            $field = $field->getChild($name);
            $value = $argument;
            $op = $operators[0] ?? null;

            if (count($operators) > 1) {
                throw new ConditionTooManyOperators(
                    array_map(
                        static function (Operator $operator): string {
                            return $operator::getName();
                        },
                        $operators,
                    ),
                );
            }
        }

        // Operator?
        if ($op === null || $value === null) {
            throw new ConditionEmpty();
        }

        // Return
        return $op->call($this, $builder, $field, $value, $context);
    }
}
