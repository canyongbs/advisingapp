<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

declare(strict_types = 1);

namespace App\GraphQL\Directives;

use App\Enums\Feature;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\AST\TypeExtensionNode;
use Illuminate\Support\Facades\Gate;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldManipulator;
use Nuwave\Lighthouse\Support\Contracts\TypeExtensionManipulator;
use Nuwave\Lighthouse\Support\Contracts\TypeManipulator;

class AddonDirective extends BaseDirective implements TypeManipulator, TypeExtensionManipulator, FieldManipulator
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Removes the item from the schema if the provided addons are not enabled.
"""
directive @addon(
  """
  Specify which addons to check
  """
  name: [String!]
) repeatable on FIELD_DEFINITION | OBJECT
GRAPHQL;
    }

    public function manipulateTypeDefinition(DocumentAST &$documentAST, TypeDefinitionNode &$typeDefinition): void
    {
        if ($this->shouldHide()) {
            $documentAST->types = collect($documentAST->types)->filter(function ($typeDefinitionNode) use ($typeDefinition) {
                return $typeDefinitionNode !== $typeDefinition;
            })->toArray();

            ASTHelper::addDirectiveToFields($this->directiveNode, $typeDefinition);
        }
    }

    public function manipulateTypeExtension(DocumentAST &$documentAST, TypeExtensionNode &$typeExtension): void
    {
        if ($this->shouldHide()) {
            $typeExtension->interfaces = new NodeList([]);
            $typeExtension->directives = new NodeList([]);
            $typeExtension->fields = new NodeList([]);

            $documentAST->typeExtensions[$typeExtension->name->value] = collect($documentAST->typeExtensions[$typeExtension->name->value])->filter(function ($typeExtensionNode) use ($typeExtension) {
                return $typeExtensionNode !== $typeExtension;
            })->toArray();
        }
    }

    public function manipulateFieldDefinition(DocumentAST &$documentAST, FieldDefinitionNode &$fieldDefinition, ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode &$parentType): void
    {
        if ($this->shouldHide()) {
            $keyToRemove = null;

            foreach ($parentType->fields as $key => $value) {
                if ($value === $fieldDefinition) {
                    $keyToRemove = $key;

                    break;
                }
            }

            unset($parentType->fields[$keyToRemove]);

            $directives = collect($fieldDefinition->directives);

            if ($directives->contains(fn ($directive) => $directive->name->value === 'paginate')) {
                $fieldDefinition->directives = new NodeList($directives->filter(fn ($directive) => $directive->name->value !== 'paginate')->toArray());

                ASTHelper::addDirectiveToNode('@all', $fieldDefinition);
            }
        }
    }

    protected function shouldHide(): bool
    {
        $feature = Feature::from($this->directiveArgValue('name'));

        return Gate::denies($feature->getGateName());
    }
}
