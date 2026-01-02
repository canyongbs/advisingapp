<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\Campaign\Jobs\ProactiveConcernCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Campaign\Models\CampaignActionEducatableRelated;
use AdvisingApp\Concern\Enums\ConcernSeverity;
use AdvisingApp\Concern\Models\ConcernStatus;
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
        ->for($group, 'group')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    $description = fake()->sentence();
    $severity = ConcernSeverity::cases()[array_rand(ConcernSeverity::cases())];
    $suggestedIntervention = fake()->sentence();
    $concernStatus = ConcernStatus::factory()->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::ProactiveConcern,
            'data' => [
                'description' => $description,
                'severity' => $severity->value,
                'suggested_intervention' => $suggestedIntervention,
                'status_id' => $concernStatus->getKey(),
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = (new ProactiveConcernCampaignActionJob($campaignActionEducatable))->withFakeBatch();

    $job->handle();

    $concerns = $educatable->concerns()->get();

    expect($concerns)->toHaveCount(1)
        ->and($concerns->first()->description)->toEqual($description)
        ->and($concerns->first()->severity)->toEqual($severity)
        ->and($concerns->first()->suggested_intervention)->toEqual($suggestedIntervention)
        ->and($concerns->first()->status_id)->toEqual($concernStatus->getKey());

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related)->toHaveCount(1);

    /** @var CampaignActionEducatableRelated $campaignActionEducatableRelated */
    $campaignActionEducatableRelated = $campaignActionEducatable->related->first();

    expect($campaignActionEducatableRelated->related->is($concerns->first()))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
