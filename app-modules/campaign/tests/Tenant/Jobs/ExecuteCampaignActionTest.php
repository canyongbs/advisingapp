<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\CareTeamCampaignActionJob;
use AdvisingApp\Campaign\Jobs\EngagementCampaignActionJob;
use AdvisingApp\Campaign\Jobs\EventCampaignActionJob;
use AdvisingApp\Campaign\Jobs\ExecuteCampaignAction;
use AdvisingApp\Campaign\Jobs\InteractionCampaignActionJob;
use AdvisingApp\Campaign\Jobs\ProactiveAlertCampaignActionJob;
use AdvisingApp\Campaign\Jobs\SubscriptionCampaignActionJob;
use AdvisingApp\Campaign\Jobs\TagsCampaignActionJob;
use AdvisingApp\Campaign\Jobs\TaskCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('dispatches the correct job based on the CampaignAction type into the batch', function (SegmentModel $segmentModel, Collection $educatables, CampaignActionType $actionType, string $jobClass) {
    Bus::fake();

    $segment = Segment::factory()->create([
        'type' => SegmentType::Static,
        'model' => $segmentModel,
    ]);

    $educatables->each(function (Model $educatable) use ($segment) {
        $segment->subjects()->create([
            'subject_id' => $educatable->getKey(),
            'subject_type' => $educatable->getMorphClass(),
        ]);
    });

    $campaign = Campaign::factory()
        ->for($segment, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => $actionType,
        ]);

    [$job, $batch] = new ExecuteCampaignAction($action)->withFakeBatch();

    assertDatabaseCount(CampaignActionEducatable::class, 0);

    $job->handle();

    expect($batch->added)
        ->toHaveCount($educatables->count())
        ->each
        ->toBeInstanceOf($jobClass);

    $educatables->each(
        fn (Model $educatable) => assertDatabaseHas(CampaignActionEducatable::class, [
            'campaign_action_id' => $action->getKey(),
            'educatable_id' => $educatable->getKey(),
            'educatable_type' => $educatable->getMorphClass(),
        ])
    );
})
    ->with([
        'prospects' => [
            SegmentModel::Prospect,
            fn () => Prospect::factory()->count(rand(1, 10))->create(),
        ],
        'students' => [
            SegmentModel::Student,
            fn () => Student::factory()->count(rand(1, 10))->create(),
        ],
    ])
    ->with([
        'Engagement Email' => [
            CampaignActionType::BulkEngagementEmail,
            EngagementCampaignActionJob::class,
        ],
        'Engagement SMS' => [
            CampaignActionType::BulkEngagementSms,
            EngagementCampaignActionJob::class,
        ],
        'Event' => [
            CampaignActionType::Event,
            EventCampaignActionJob::class,
        ],
        'Alert' => [
            CampaignActionType::ProactiveAlert,
            ProactiveAlertCampaignActionJob::class,
        ],
        'Interaction' => [
            CampaignActionType::Interaction,
            InteractionCampaignActionJob::class,
        ],
        'Care Team' => [
            CampaignActionType::CareTeam,
            CareTeamCampaignActionJob::class,
        ],
        'Task' => [
            CampaignActionType::Task,
            TaskCampaignActionJob::class,
        ],
        'Subscription' => [
            CampaignActionType::Subscription,
            SubscriptionCampaignActionJob::class,
        ],
        'Tags' => [
            CampaignActionType::Tags,
            TagsCampaignActionJob::class,
        ],
    ]);

// Test when the CampaignActionEducatable already exists
