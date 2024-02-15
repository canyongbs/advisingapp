<?php

namespace App\Overrides\LastDragon_ru\LaraASP\GraphQL\SearchBy\Types;

use App\GraphQL\Directives\CanUseInQueryDirective;
use Nuwave\Lighthouse\Support\Contracts\Directive;
use LastDragon_ru\LaraASP\GraphQL\Builder\Manipulator;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Types\Condition as BaseCondition;

class Condition extends BaseCondition
{
    protected function isFieldDirectiveAllowed(Manipulator $manipulator, Directive $directive): bool
    {
        if ($directive instanceof CanUseInQueryDirective) {
            return true;
        }

        return parent::isFieldDirectiveAllowed($manipulator, $directive);
    }
}
