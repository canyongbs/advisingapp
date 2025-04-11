<?php

namespace AdvisingApp\Prospect\GraphQL\Directives;

use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Relations\Relation;
use Nuwave\Lighthouse\Execution\Arguments\SaveModel;
use Nuwave\Lighthouse\Schema\Directives\OneModelMutationDirective;

class CreateProspectDirective extends OneModelMutationDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Create a new prospect with the given arguments.
"""
directive @createProspect(
  """
  Specify the name of the relation on the parent model.
  This is only needed when using this directive as a nested arg
  resolver and if the name of the relation is not the arg name.
  """
  relation: String
) on FIELD_DEFINITION | ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
GRAPHQL;
    }

    protected function makeExecutionFunction(?Relation $parentRelation = null): callable
    {
        return new SaveModel($parentRelation);
    }

    protected function getModelClass(string $argumentName = 'model'): string
    {
        return Prospect::class;
    }
}
