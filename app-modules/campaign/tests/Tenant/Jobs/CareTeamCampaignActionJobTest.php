<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\CareTeamCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the segment', function (Educatable $educatable, array $priorCareTeam, bool $removePrior) {
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

    $educatable->careTeam()->sync($priorCareTeam);

    $campaign = Campaign::factory()
        ->for($segment, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    $users = User::factory()->licensed(LicenseType::cases())->count(3)->create();

    $careTeam = [];

    foreach ($users as $user) {
        $careTeam[] = ['user_id' => $user->id, 'care_team_role_id' => null];
    }

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::CareTeam,
            'data' => [
                'careTeam' => $careTeam,
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

    [$job] = new CareTeamCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    expect(
        $educatable->careTeam->pluck('id')->toArray()
    )
        ->toBe(
            $removePrior
                ? $users->pluck('id')->toArray()
                : [...$priorCareTeam, ...$users->pluck('id')->toArray()]
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
            'no prior care team | prospects | remove prior false' => [
                [],
                false,
            ],
            'no prior care team | prospects | remove prior true' => [
                [],
                true,
            ],
            'prior care team | prospects | remove prior false' => [
                fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
                false,
            ],
            'prior care team | prospects | remove prior true' => [
                fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
                true,
            ],
            'no prior care team | students | remove prior false' => [
                [],
                false,
            ],
            'no prior care team | students | remove prior true' => [
                [],
                true,
            ],
            'prior care team | students | remove prior false' => [
                fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
                false,
            ],
            'prior care team | students | remove prior true' => [
                fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
                true,
            ],
        ]
    );
