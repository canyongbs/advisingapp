<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\CaseCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the segment', function (Educatable $educatable) {
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

    $campaign = Campaign::factory()
        ->for($segment, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    $closeDetails = fake()->sentence();
    $resDetails = fake()->sentence();
    $division = Division::factory()->create();
    $caseStatus = CaseStatus::factory()->create();
    $casePriority = CasePriority::factory()->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Interaction,
            'data' => [
                'close_details' => $closeDetails,
                'res_details' => $resDetails,
                'division_id' => $division->getKey(),
                'status_id' => $caseStatus->getKey(),
                'priority_id' => $casePriority->getKey(),
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new CaseCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    $cases = $educatable->cases()->get();

    expect($cases)->toHaveCount(1)
        ->and($cases->first()->close_details)->toEqual($closeDetails)
        ->and($cases->first()->res_details)->toEqual($resDetails)
        ->and($cases->first()->division->getKey())->toEqual($division->getKey())
        ->and($cases->first()->status->getKey())->toEqual($caseStatus->getKey())
        ->and($cases->first()->priority->getKey())->toEqual($casePriority->getKey())
        ->and($cases->first()->createdBy->getKey())->toEqual($campaign->createdBy->getKey());

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related->is($cases->first()))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);

it('will create the proper assignment if provided', function (Educatable $educatable) {
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

    $campaign = Campaign::factory()
        ->for($segment, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    $closeDetails = fake()->sentence();
    $resDetails = fake()->sentence();
    $division = Division::factory()->create();
    $caseStatus = CaseStatus::factory()->create();
    $casePriority = CasePriority::factory()->create();
    $assignedToUser = User::factory()->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Interaction,
            'data' => [
                'close_details' => $closeDetails,
                'res_details' => $resDetails,
                'division_id' => $division->getKey(),
                'status_id' => $caseStatus->getKey(),
                'priority_id' => $casePriority->getKey(),
                'assigned_to_id' => $assignedToUser->getKey(),
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new CaseCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    $cases = $educatable->cases()->get();

    expect($cases)->toHaveCount(1)
        ->and($cases->first()->assignments)->toHaveCount(1)
        ->and($cases->first()->assignments->first()->user_id)->toEqual($assignedToUser->getKey())
        ->and($cases->first()->assignments->first()->assigned_by_id)->toEqual($campaign->createdBy->getKey())
        ->and($cases->first()->assignments->first()->status)->toEqual(CaseAssignmentStatus::Active);

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related->is($cases->first()))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
