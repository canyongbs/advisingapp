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
use AdvisingApp\Campaign\Jobs\InteractionCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Campaign\Models\CampaignActionEducatableRelated;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Group\Enums\SegmentModel;
use AdvisingApp\Group\Enums\SegmentType;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the group', function (Educatable $educatable) {
    Bus::fake();

    /** @var Group $group */
    $group = Group::factory()->create([
        'type' => SegmentType::Static,
        'model' => match ($educatable::class) {
            Student::class => SegmentModel::Student,
            Prospect::class => SegmentModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $campaign = Campaign::factory()
        ->for($group, 'segment')
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

    [$job] = (new InteractionCampaignActionJob($campaignActionEducatable))->withFakeBatch();

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
        ->and($campaignActionEducatable->related)->toHaveCount(1);

    /** @var CampaignActionEducatableRelated $campaignActionEducatableRelated */
    $campaignActionEducatableRelated = $campaignActionEducatable->related->first();

    expect($campaignActionEducatableRelated->related->is($interactions->first()))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
