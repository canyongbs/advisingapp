<?php

namespace AdvisingApp\Engagement\GraphQL\Mutations;

use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Engagement\Models\Engagement;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteEngagement
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Engagement
    {
        $engagement = Engagement::findOrFail($args['id']);

        $engagement->deliverable->delete();

        $engagement->delete();

        return $engagement->refresh();
    }
}
