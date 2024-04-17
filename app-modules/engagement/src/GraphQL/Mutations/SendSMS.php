<?php

namespace AdvisingApp\Engagement\GraphQL\Mutations;

use Illuminate\Database\Eloquent\Model;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AdvisingApp\Engagement\Actions\GenerateTipTapBodyJson;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Actions\CreateEngagementDeliverable;

class SendSMS
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Engagement
    {
        /** @var Model $morph */
        $morph = Relation::getMorphedModel($args['recipient_type']);
        $recipient = $morph::find($args['recipient_id']);

        $mergeTags = match ($recipient::class) {
            Student::class => [
                '{{ student full name }}',
                '{{ student email }}',
            ],
            default => [],
        };

        $args['body'] = app(GenerateTipTapBodyJson::class)(body: $args['body'], mergeData: $mergeTags);

        $engagement = Engagement::create($args);

        app(CreateEngagementDeliverable::class)(engagement: $engagement, deliveryMethod: EngagementDeliveryMethod::Sms->value);

        return $engagement->refresh();
    }
}
