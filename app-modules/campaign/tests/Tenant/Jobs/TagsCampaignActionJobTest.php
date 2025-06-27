<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\TagsCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Campaign\Models\CampaignActionEducatableRelated;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\TagType;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the segment', function (Educatable $educatable, bool $priorTags, bool $removePrior) {
    Bus::fake();

    /** @var Segment $segment */
    $segment = Segment::factory()->create([
        'type' => SegmentType::Static,
        'model' => match ($educatable::class) {
            Student::class => SegmentModel::Student,
            Prospect::class => SegmentModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $priorTagModels = Tag::factory()
        ->count(3)
        ->create([
            'type' => $educatable instanceof Student
                ? TagType::Student
                : TagType::Prospect,
        ]);

    if ($priorTags) {
        $educatable->tags()->sync($priorTagModels);
    }

    $campaign = Campaign::factory()
        ->for($segment, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    $tags = Tag::factory()
        ->count(3)
        ->create([
            'type' => $educatable instanceof Student
                ? TagType::Student
                : TagType::Prospect,
        ]);

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Tags,
            'data' => [
                'tag_ids' => $tags->pluck('id')->toArray(),
                'remove_prior' => $removePrior,
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = (new TagsCampaignActionJob($campaignActionEducatable))->withFakeBatch();

    $job->handle();

    expect(
        $educatable->tags()->pluck('tags.id')->toArray()
    )
        ->toEqualCanonicalizing(
            $removePrior
                ? $tags->pluck('id')->toArray()
                : [...($priorTags ? $priorTagModels->pluck('id')->toArray() : []), ...$tags->pluck('id')->toArray()]
        );

    $campaignActionEducatable->refresh();

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    expect($campaignActionEducatable->related)->toHaveCount($tags->count());

    $campaignActionEducatable->related
        // @phpstan-ignore argument.type
        ->each(function (CampaignActionEducatableRelated $related) use ($tags, $educatable) {
            $relatedRelated = $related->related;

            expect($relatedRelated)->toBeInstanceOf(Taggable::class);

            /** @var Taggable $relatedRelated */
            expect($relatedRelated->tag->getKey())->toBeIn($tags->pluck('id'))
                ->and($relatedRelated->taggable_type)->toEqual($educatable->getMorphClass())
                ->and($relatedRelated->taggable_id)->toEqual($educatable->getKey());
        });
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ])
    ->with(
        [
            'no prior tags | remove prior false' => [
                false,
                false,
            ],
            'no prior tags | remove prior true' => [
                false,
                true,
            ],
            'prior tags | remove prior false' => [
                true,
                false,
            ],
            'prior tags | remove prior true' => [
                true,
                true,
            ],
        ]
    );
