<?php

namespace App\GraphQL\Scalars;

use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;

class JSON extends ScalarType
{
    public function serialize($value)
    {
        return json_encode($value);
    }

    public function parseValue($value)
    {
        return json_decode($value);
    }

    public function parseLiteral(Node $valueNode, array $variables = null)
    {
        return json_decode($valueNode->value);
    }
}
