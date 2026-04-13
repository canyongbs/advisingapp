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
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\ViewCampaign;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\RelationManagers\CampaignActionsRelationManager;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can edit a non-confidential task campaign journey step without error', function () {
    asSuperAdmin();

    $campaign = Campaign::factory()
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    $assignedTo = User::factory()->create();

    $updatedTitle = 'Updated Title';
    $updatedDescription = 'This is an updated description.';
    $executeAt = now()->addDays(2)->toDateTimeString();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Task,
            'execute_at' => now()->addDay(),
            'data' => [
                'title' => 'Title',
                'description' => 'This is a description.',
                'due' => null,
                'assigned_to' => $assignedTo->getKey(),
                'is_confidential' => false,
            ],
        ]);

    livewire(CampaignActionsRelationManager::class, [
        'ownerRecord' => $campaign,
        'pageClass' => ViewCampaign::class,
    ])
        ->callTableAction('edit', record: $action->getKey(), data: [
            'execute_at' => $executeAt,
            'data' => [
                'title' => $updatedTitle,
                'description' => $updatedDescription,
                'due' => null,
                'assigned_to' => $assignedTo->getKey(),
                'is_confidential' => false,
                'confidential_task_projects' => [],
                'confidential_task_users' => [],
                'confidential_task_teams' => [],
            ],
        ])
        ->assertHasNoTableActionErrors();

    $action->refresh();

    expect($action->data['title'])->toEqual($updatedTitle)
        ->and($action->data['description'])->toEqual($updatedDescription)
        ->and($action->data['is_confidential'])->toBeFalse();
});

it('can edit a confidential task campaign journey step and persist confidential access data', function () {
    asSuperAdmin();

    $campaign = Campaign::factory()
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    $assignedTo = User::factory()->create();
    $projects = Project::factory()->count(2)->create();
    $users = User::factory()->count(2)->create();
    $teams = Team::factory()->count(2)->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Task,
            'execute_at' => now()->addDay(),
            'data' => [
                'title' => 'Title',
                'description' => 'This is a description.',
                'due' => null,
                'assigned_to' => $assignedTo->getKey(),
                'is_confidential' => false,
            ],
        ]);

    $updatedTitle = 'Updated Title';
    $updatedDescription = 'This is an updated description.';

    livewire(CampaignActionsRelationManager::class, [
        'ownerRecord' => $campaign,
        'pageClass' => ViewCampaign::class,
    ])
        ->callTableAction('edit', record: $action->getKey(), data: [
            'execute_at' => now()->addDays(2)->toDateTimeString(),
            'data' => [
                'title' => $updatedTitle,
                'description' => $updatedDescription,
                'due' => null,
                'assigned_to' => $assignedTo->getKey(),
                'is_confidential' => true,
                'confidential_task_projects' => $projects->pluck('id')->toArray(),
                'confidential_task_users' => $users->pluck('id')->toArray(),
                'confidential_task_teams' => $teams->pluck('id')->toArray(),
            ],
        ])
        ->assertHasNoTableActionErrors();

    $action->refresh();

    expect($action->data['title'])->toEqual($updatedTitle)
        ->and($action->data['is_confidential'])->toBeTrue()
        ->and($action->data['confidential_task_projects'])->toEqual($projects->pluck('id')->toArray())
        ->and($action->data['confidential_task_users'])->toEqual($users->pluck('id')->toArray())
        ->and($action->data['confidential_task_teams'])->toEqual($teams->pluck('id')->toArray());
});
