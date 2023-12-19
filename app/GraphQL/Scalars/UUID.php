<?php

declare(strict_types = 1);

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Utils\Utils;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\Type;
use GraphQL\Language\AST\ValueNode;
use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use LastDragon_ru\LaraASP\GraphQL\Builder\BuilderInfo;
use LastDragon_ru\LaraASP\GraphQL\Builder\Manipulator;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\TypeSource;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\TypeDefinition;

/** Read more about scalars here: https://webonyx.github.io/graphql-php/type-definitions/scalars. */
final class UUID extends ScalarType implements TypeDefinition
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

    public function getTypeName(Manipulator $manipulator, BuilderInfo $builder, TypeSource $source): string
    {
        return $this->name();
    }

    public function getTypeDefinition(
        Manipulator $manipulator,
        string $name,
        TypeSource $source,
    ): TypeDefinitionNode|Type|null {
        return $this;
    }

    private function validateUUID($value): false|int
    {
        $pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

        return preg_match($pattern, $value);
    }
}
