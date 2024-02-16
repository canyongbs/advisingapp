<?php

namespace App\GraphQL\Scalars;

use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Illuminate\Support\Carbon;

class Year extends ScalarType
{
    public function serialize($value): ?int
    {
        return $value;
    }

    public function parseValue($value): int
    {
        return Carbon::parse($value)->year;
    }

    public function parseLiteral(Node $valueNode, array $variables = null): int
    {
        return Carbon::parse($valueNode->value)->year;
    }
}
