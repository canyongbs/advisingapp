<?php

namespace AdvisingApp\Engagement\GraphQL\Mutations;

use AdvisingApp\Engagement\Actions\CreateEngagementDeliverable;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Models\Engagement;
use App\Models\User;
use Nuwave\Lighthouse\Execution\ResolveInfo;
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
