<?php

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\ProactiveAlertCampaignActionJob;
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

    $description = fake()->sentence();
    $severity = AlertSeverity::cases()[array_rand(AlertSeverity::cases())];
    $suggestedIntervention = fake()->sentence();
    $alertStatus = AlertStatus::factory()->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::ProactiveAlert,
            'data' => [
                'description' => $description,
                'severity' => $severity->value,
                'suggested_intervention' => $suggestedIntervention,
                'status_id' => $alertStatus->getKey(),
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new ProactiveAlertCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    $alerts = $educatable->alerts()->get();

    expect($alerts)->toHaveCount(1)
        ->and($alerts->first()->description)->toEqual($description)
        ->and($alerts->first()->severity)->toEqual($severity)
        ->and($alerts->first()->suggested_intervention)->toEqual($suggestedIntervention)
        ->and($alerts->first()->status_id)->toEqual($alertStatus->getKey());

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related->is($alerts->first()))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
