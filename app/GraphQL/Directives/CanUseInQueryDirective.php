<?php

namespace App\GraphQL\Directives;

use RuntimeException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;

final class CanUseInQueryDirective extends BaseDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Check a Laravel Policy to ensure the current user is authorized to use this field in a query.
"""
directive @canUseInQuery(
  """
  The ability to check permissions for.
  """
  ability: String!

) repeatable on FIELD_DEFINITION
GRAPHQL;
    }

    public function authorize(object $builder): void
    {
        if (! $builder instanceof Builder) {
            throw new RuntimeException('Query must be instance of [' . Builder::class . '] in order to extract the model for [@canUseInQuery].');
        }

        $arguments = $this->directiveNode->arguments;

        if (! $arguments->offsetExists(0)) {
            throw new RuntimeException('[@canUseInQuery] directive must have at least one ability to check.');
        }

        Gate::authorize($arguments->offsetGet(0)->value->value, $builder->getModel());
    }
}
