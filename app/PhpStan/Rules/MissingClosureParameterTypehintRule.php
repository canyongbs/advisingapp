<?php

namespace App\PhpStan\Rules;

use function is_string;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Closure>
 */
final class MissingClosureParameterTypehintRule implements Rule
{
    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @return RuleError[] errors
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $messages = [];

        foreach ($node->params as $index => $param) {
            if (null !== $param->type || ! $param->var instanceof Node\Expr\Variable || ! is_string($param->var->name)) {
                continue;
            }

            $messages[] = RuleErrorBuilder::message(sprintf('Parameter #%d $%s of anonymous function has no typehint.', 1 + $index, $param->var->name))
                ->identifier('closure.parameterMissingTypehint')
                ->build();
        }

        return $messages;
    }
}
