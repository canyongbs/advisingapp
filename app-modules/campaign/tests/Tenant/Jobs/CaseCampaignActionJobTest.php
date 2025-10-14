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
use AdvisingApp\Campaign\Jobs\CaseCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Campaign\Models\CampaignActionEducatableRelated;
use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the group', function (Educatable $educatable) {
    Bus::fake();

    /** @var Group $group */
    $group = Group::factory()->create([
        'type' => GroupType::Static,
        'model' => match ($educatable::class) {
            Student::class => GroupModel::Student,
            Prospect::class => GroupModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $campaign = Campaign::factory()
        ->for($group, 'segment')
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
            'type' => CampaignActionType::Case,
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

    [$job] = (new CaseCampaignActionJob($campaignActionEducatable))->withFakeBatch();

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
        ->and($campaignActionEducatable->related)->toHaveCount(1);

    /** @var CampaignActionEducatableRelated $campaignActionEducatableRelated */
    $campaignActionEducatableRelated = $campaignActionEducatable->related->first();

    expect($campaignActionEducatableRelated->related->is($cases->first()))->toBeTrue();
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

    /** @var Segment $group */
    $group = Group::factory()->create([
        'type' => GroupType::Static,
        'model' => match ($educatable::class) {
            Student::class => GroupModel::Student,
            Prospect::class => GroupModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $campaign = Campaign::factory()
        ->for($group, 'segment')
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

    [$job] = (new CaseCampaignActionJob($campaignActionEducatable))->withFakeBatch();

    $job->handle();

    $cases = $educatable->cases()->get();

    expect($cases)->toHaveCount(1)
        ->and($cases->first()->assignments)->toHaveCount(1)
        ->and($cases->first()->assignments->first()->user_id)->toEqual($assignedToUser->getKey())
        ->and($cases->first()->assignments->first()->assigned_by_id)->toEqual($campaign->createdBy->getKey())
        ->and($cases->first()->assignments->first()->status)->toEqual(CaseAssignmentStatus::Active);

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related)->toHaveCount(1);

    /** @var CampaignActionEducatableRelated $campaignActionEducatableRelated */
    $campaignActionEducatableRelated = $campaignActionEducatable->related->first();

    expect($campaignActionEducatableRelated->related->is($cases->first()))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
