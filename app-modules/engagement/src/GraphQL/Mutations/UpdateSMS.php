<?php

namespace AdvisingApp\Engagement\GraphQL\Mutations;

use App\Models\User;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Engagement\Models\Engagement;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Actions\CreateEngagementDeliverable;

class UpdateSMS
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Engagement
    {
        ray($args['body'], is_string($args['body']));

        $args['body'] = tiptap_converter()->asJSON($args['body'], true);

        $engagement = Engagement::findOrFail($args['id']);
        $engagement->fill($args);

        $engagement->deliverable->delete();
        app(CreateEngagementDeliverable::class)(engagement: $engagement, deliveryMethod: EngagementDeliveryMethod::Sms->value);

        return $engagement->refresh();
    }
}
