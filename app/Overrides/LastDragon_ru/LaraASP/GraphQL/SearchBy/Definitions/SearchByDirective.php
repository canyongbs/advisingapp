<?php

namespace App\Overrides\LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions;

use App\GraphQL\Directives\CanUseInQueryDirective;
use LastDragon_ru\LaraASP\GraphQL\Builder\Property;
use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;
use LastDragon_ru\LaraASP\GraphQL\Utils\ArgumentFactory;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\Operator;
use LastDragon_ru\LaraASP\GraphQL\Builder\Exceptions\Client\ConditionEmpty;
use LastDragon_ru\LaraASP\GraphQL\Builder\Exceptions\Client\ConditionTooManyOperators;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions\SearchByDirective as BaseSearchByDirective;

class SearchByDirective extends BaseSearchByDirective
{
    /**
     * @template T of object
     *
     * @param T $builder
     *
     * @return T
     */
    protected function call(object $builder, Property $property, ArgumentSet $operator): object
    {
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

            $property = $property->getChild($name);
            $value = $argument;
            $op = reset($operators);

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
        if (! $op || ! $value) {
            throw new ConditionEmpty();
        }

        // Return
        return $op->call($this, $builder, $property, $value);
    }
}
