<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\TagsCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\TagType;
use App\Models\Tag;
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

    [$job] = new TagsCampaignActionJob($campaignActionEducatable)->withFakeBatch();

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
