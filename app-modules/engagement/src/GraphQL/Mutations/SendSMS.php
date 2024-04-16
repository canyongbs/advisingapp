<?php

namespace AdvisingApp\Engagement\GraphQL\Mutations;

use Illuminate\Database\Eloquent\Model;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Actions\CreateEngagementDeliverable;

class SendSMS
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Engagement
    {
        // $converter = tiptap_converter();

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

        // "Hello <span> student full name </span> with <span>student email</span>";

        // preg_match_all('/{{[\s\S]*?}}/', $args['body'], $matches);
        //
        // rd($matches);

        $body = str(nl2br($args['body']))->ray();
        // $body = str($args['body'])
        //     ->replaceMatches('/{{[\s\S]*?}}/', function ($match) use ($mergeTags) {
        //         ray($match);
        //
        //         if (! in_array($match[0], $mergeTags)) {
        //             return $match[0];
        //         }
        //
        //         return sprintf(
        //             '<span data-type="mergeTag" data-id="%s">%s</span>',
        //             str($match[0])->remove(['{{', '}}'])->trim()->toString(),
        //             $match[0]
        //         );
        //     })->wrap('<div>', '</div>');
        // ->ray()
        // ->explode('<br />')
        // ->ray()
        // ->map(fn ($line) => "<p>{$line}</p>")
        // ->ray();

        rd($body);

        $args['body'] = tiptap_converter()->mergeTagsMap(['student full name', 'student email'])->asJSON($body, true);

        $engagement = Engagement::create($args);

        app(CreateEngagementDeliverable::class)(engagement: $engagement, deliveryMethod: EngagementDeliveryMethod::Sms->value);

        return $engagement->refresh();
    }
}
