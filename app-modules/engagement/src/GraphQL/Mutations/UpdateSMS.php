<?php

namespace AdvisingApp\Engagement\GraphQL\Mutations;

use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AdvisingApp\Engagement\Actions\GenerateTipTapBodyJson;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Actions\CreateEngagementDeliverable;

class UpdateSMS
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Engagement
    {
        $engagement = Engagement::findOrFail($args['id']);

        $mergeTags = match ($engagement->recipient::class) {
            Student::class => [
                '{{ student full name }}',
                '{{ student email }}',
            ],
            default => [],
        };

        $args['body'] = app(GenerateTipTapBodyJson::class)(body: $args['body'], mergeData: $mergeTags);
        $engagement->update($args);

        $engagement->deliverable->delete();
        app(CreateEngagementDeliverable::class)(engagement: $engagement, deliveryMethod: EngagementDeliveryMethod::Sms->value);

        return $engagement->refresh();
    }
}
