<?php

declare(strict_types = 1);

namespace App\GraphQL\Directives;

use GraphQL\Language\AST\TypeExtensionNode;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use GraphQL\Language\AST\TypeDefinitionNode;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\TypeManipulator;
use Nuwave\Lighthouse\Support\Contracts\TypeExtensionManipulator;

final class AfeatureDirective extends BaseDirective implements TypeManipulator, TypeExtensionManipulator
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Remove type if feature is not enabled.
"""
directive @afeature(
  """
  Specify which feature to use
  """
  feature: [String!]
) repeatable on FIELD_DEFINITION | OBJECT
GRAPHQL;
    }

    public function manipulateTypeDefinition(DocumentAST &$documentAST, TypeDefinitionNode &$typeDefinition): void
    {
        $feature = $this->directiveArgValue('feature');
        ray($documentAST);
        ray($typeDefinition);

        unset($documentAST->types[$typeDefinition->name->value]);
        //ASTHelper::addDirectiveToFields($this->directiveNode, $typeDefinition);
    }

    public function manipulateTypeExtension(DocumentAST &$documentAST, TypeExtensionNode &$typeExtension): void
    {
        ray($documentAST);
        ray($typeExtension);

        $documentAST->typeExtensions = collect($documentAST->typeExtensions)->filter(function ($value, $key) use ($typeExtension, $documentAST) {
            if ($key !== $typeExtension->name->value) {
                $documentAST->typeExtensions[$key] = $value;
            }
        })->toArray();
        ASTHelper::addDirectiveToFields($this->directiveNode, $typeExtension);
    }
}
