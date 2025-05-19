<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\InteractionCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
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

    $interactionType = InteractionType::factory()->create();
    $interactionInitiative = InteractionInitiative::factory()->create();
    $interactionRelation = InteractionRelation::factory()->create();
    $interactionDriver = InteractionDriver::factory()->create();
    $interactionStatus = InteractionStatus::factory()->create();
    $interactionOutcome = InteractionOutcome::factory()->create();
    $division = Division::factory()->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Interaction,
            'data' => [
                'interaction_type_id' => $interactionType->getKey(),
                'interaction_initiative_id' => $interactionInitiative->getKey(),
                'interaction_relation_id' => $interactionRelation->getKey(),
                'interaction_driver_id' => $interactionDriver->getKey(),
                'interaction_status_id' => $interactionStatus->getKey(),
                'interaction_outcome_id' => $interactionOutcome->getKey(),
                'division_id' => $division->getKey(),
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new InteractionCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    $interactions = $educatable->interactions()->get();

    expect($interactions)->toHaveCount(1)
        ->and($interactions->first()->type->getKey())->toEqual($interactionType->getKey())
        ->and($interactions->first()->initiative->getKey())->toEqual($interactionInitiative->getKey())
        ->and($interactions->first()->relation->getKey())->toEqual($interactionRelation->getKey())
        ->and($interactions->first()->driver->getKey())->toEqual($interactionDriver->getKey())
        ->and($interactions->first()->status->getKey())->toEqual($interactionStatus->getKey())
        ->and($interactions->first()->outcome->getKey())->toEqual($interactionOutcome->getKey())
        ->and($interactions->first()->division->getKey())->toEqual($division->getKey());

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related->is($interactions->first()))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
