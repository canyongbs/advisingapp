<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\GraphQL\Mutations;

use Illuminate\Support\Stringable;
use AdvisingApp\Prospect\Models\Prospect;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\Notification\Enums\NotificationChannel;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AdvisingApp\Engagement\Actions\GenerateTipTapBodyJson;
use AdvisingApp\Engagement\Actions\CreateEngagementDeliverable;

class SendEmail
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Engagement
    {
        /** @var Student|Prospect $morph */
        $morph = Relation::getMorphedModel($args['recipient_type']);

        $mergeTags = collect(Engagement::getMergeTags($morph))
            ->map(fn (string $tag): string => "{{ {$tag} }}")
            ->toArray();

        $body = str($args['body'])
            ->when(strip_tags($args['body']) === $args['body'], fn (Stringable $str) => $str->markdown([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]))
            ->toString();

        $args['body'] = app(GenerateTipTapBodyJson::class)(body: $body, mergeTags: $mergeTags);

        $engagement = Engagement::create($args);

        app(CreateEngagementDeliverable::class)(engagement: $engagement, deliveryMethod: NotificationChannel::Email->value);

        return $engagement->refresh();
    }
}
