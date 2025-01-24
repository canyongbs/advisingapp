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

declare(strict_types = 1);

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\Context;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\TypeDefinition;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\TypeSource;
use LastDragon_ru\LaraASP\GraphQL\Builder\Manipulator;

/** Read more about scalars here: https://webonyx.github.io/graphql-php/type-definitions/scalars. */
class UUID extends ScalarType implements TypeDefinition
{
    public string $name = 'UUID';

    public ?string $description = 'Represents a uniquely identifying string.';

    /** Serializes an internal value to include in a response. */
    public function serialize(mixed $value): string
    {
        if (! $this->validateUUID($value)) {
            throw new InvariantViolation('Could not serialize following value as UUID: ' . Utils::printSafe($value));
        }

        // Assuming the internal representation of the value is always correct
        return $value;
    }

    /** Parses an externally provided value (query variable) to use as an input. */
    public function parseValue(mixed $value): string
    {
        if (! $this->validateUUID($value)) {
            throw new InvariantViolation('Cannot represent following value as UUID: ' . Utils::printSafe($value));
        }

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * Should throw an exception with a client friendly message on invalid value nodes, @param  ValueNode&Node $valueNode
     *
     * @param  array<string, mixed>|null  $variables
     *
     * @throws Error
     *
     * @see \GraphQL\Error\ClientAware.
     *
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): string
    {
        // Throw GraphQL\Error\Error vs \UnexpectedValueException to locate the error in the query
        if (! $valueNode instanceof StringValueNode) {
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }

        if (! $this->validateUUID($valueNode->value)) {
            throw new Error('Not a valid UUID', [$valueNode]);
        }

        return $valueNode->value;
    }

    public function getTypeName(TypeSource $source, Context $context): string
    {
        return $this->name();
    }

    public function getTypeDefinition(
        Manipulator $manipulator,
        TypeSource $source,
        Context $context,
        string $name,
    ): (TypeDefinitionNode&Node)|string|null {
        return $this;
    }

    private function validateUUID($value): false|int
    {
        $pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

        return preg_match($pattern, $value);
    }
}
