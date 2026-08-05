<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Tests\Tenant\Filament\Resources\Campaigns\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\CampaignResource;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\ListCampaigns;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Model;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can view the all campaigns in the list page', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin();

    Campaign::factory(2)->enabled()->create();
    Campaign::factory()->disabled()->create();
    Campaign::factory()->for($user, 'createdBy')
        ->create();
    Campaign::factory()
        ->has(CampaignAction::factory()->finishedAt(), 'actions')
        ->create();
    Campaign::factory()
        ->has(CampaignAction::factory(), 'actions')
        ->create();

    livewire(ListCampaigns::class)
        ->set('tableRecordsPerPage', 20)
        ->assertCanSeeTableRecords(Campaign::all())
        ->assertCountTableRecords(6);
});

it('can filter campaigns by `My Campaigns`', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    asSuperAdmin($user);

    $expectedCampaign = Campaign::factory()
        ->for($user, 'createdBy')
        ->create();

    $filteredOutCampaigns = Campaign::factory()->count(2)->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([
            $expectedCampaign,
            ...$filteredOutCampaigns,
        ])
        ->filterTable('My Campaigns')
        ->assertCanSeeTableRecords([$expectedCampaign])
        ->assertCanNotSeeTableRecords($filteredOutCampaigns);
});

it('can filter campaigns by `Enabled`', function () {
    asSuperAdmin();

    $enabledCampaigns = Campaign::factory()->count(2)->enabled()->create();
    $disabledCampaigns = Campaign::factory()->count(2)->disabled()->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([
            ...$enabledCampaigns,
            ...$disabledCampaigns,
        ])
        ->filterTable('Enabled')
        ->assertCanSeeTableRecords($enabledCampaigns)
        ->assertCanNotSeeTableRecords($disabledCampaigns);
});

it('can filter campaigns by `Disabled`', function () {
    asSuperAdmin();

    $enabledCampaigns = Campaign::factory()->count(2)->enabled()->create();
    $disabledCampaigns = Campaign::factory()->count(2)->disabled()->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([
            ...$enabledCampaigns,
            ...$disabledCampaigns,
        ])
        ->filterTable('Enabled', false) // Filter by Disabled
        ->assertCanSeeTableRecords($disabledCampaigns)
        ->assertCanNotSeeTableRecords($enabledCampaigns);
});

it('can filter campaigns by `Completed`', function () {
    asSuperAdmin();

    $completeCampaign = Campaign::factory()
        ->has(CampaignAction::factory()->finishedAt(), 'actions')
        ->create();

    $partiallyCompleteCampaign = Campaign::factory()
        ->has(CampaignAction::factory()->finishedAt(), 'actions')
        ->has(CampaignAction::factory(), 'actions')
        ->create();

    $incompleteCampaign = Campaign::factory()
        ->has(CampaignAction::factory(), 'actions')
        ->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([
            $completeCampaign,
            $partiallyCompleteCampaign,
            $incompleteCampaign,
        ])
        ->filterTable('Completed')
        ->assertCanSeeTableRecords([$completeCampaign])
        ->assertCanNotSeeTableRecords([
            $partiallyCompleteCampaign,
            $incompleteCampaign,
        ]);
});

it('can filter campaigns by `In Progress`', function () {
    asSuperAdmin();

    $completeCampaign = Campaign::factory()
        ->has(CampaignAction::factory()->finishedAt(), 'actions')
        ->create();

    $partiallyCompleteCampaign = Campaign::factory()
        ->has(CampaignAction::factory()->finishedAt(), 'actions')
        ->has(CampaignAction::factory(), 'actions')
        ->create();

    $incompleteCampaign = Campaign::factory()
        ->has(CampaignAction::factory(), 'actions')
        ->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([
            $completeCampaign,
            $partiallyCompleteCampaign,
            $incompleteCampaign,
        ])
        ->filterTable('Completed', false) // Filter by In Progress
        ->assertCanSeeTableRecords([
            $partiallyCompleteCampaign,
            $incompleteCampaign,
        ])
        ->assertCanNotSeeTableRecords([
            $completeCampaign,
        ]);
});

it('excludes archived campaigns from the list', function () {
    asSuperAdmin();

    $activeCampaign = Campaign::factory()->create();
    $archivedCampaign = Campaign::factory()->create();

    $archivedCampaign->archive();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([$activeCampaign])
        ->assertCanNotSeeTableRecords([$archivedCampaign])
        ->assertCountTableRecords(1);
});

it('shows the group name and population as the name column description for a static group', function (GroupModel $model, int $memberCount, string $expectedLabel) {
    asSuperAdmin();

    $modelClass = $model->class();

    $members = $modelClass::factory()->count($memberCount)->create();
    $modelClass::factory()->count(2)->create();

    $group = Group::factory()->static()->create([
        'name' => 'Everyone',
        'model' => $model,
    ]);

    $members->each(fn (Model $member) => $group->subjects()->create([
        'subject_id' => $member->getKey(),
        'subject_type' => $member->getMorphClass(),
    ]));

    $campaign = Campaign::factory()->create([
        'segment_id' => $group->getKey(),
    ]);

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([$campaign])
        ->assertSee("Everyone ({$memberCount} {$expectedLabel})");
})->with([
    'students' => [GroupModel::Student, 3, 'Students'],
    'a single student' => [GroupModel::Student, 1, 'Student'],
    'prospects' => [GroupModel::Prospect, 3, 'Prospects'],
    'a single prospect' => [GroupModel::Prospect, 1, 'Prospect'],
]);

it('shows the group name and population as the name column description for a dynamic group', function (GroupModel $model, string $expectedLabel, string $attribute) {
    asSuperAdmin();

    $modelClass = $model->class();

    $modelClass::factory()->count(4)->create([$attribute => 'Included']);
    $modelClass::factory()->count(2)->create([$attribute => 'Excluded']);

    $group = Group::factory()->dynamic()->create([
        'name' => 'Everyone',
        'model' => $model,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'lastName' => [
                        'type' => $attribute,
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => 'Included',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $campaign = Campaign::factory()->create([
        'segment_id' => $group->getKey(),
    ]);

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([$campaign])
        ->assertSee("Everyone (4 {$expectedLabel})");
})->with([
    'students' => [GroupModel::Student, 'Students', 'last'],
    'prospects' => [GroupModel::Prospect, 'Prospects', 'last_name'],
]);

it('archives and disables every selected campaign without deleting any', function () {
    asSuperAdmin();

    $executed = Campaign::factory()->enabled()
        ->has(CampaignAction::factory()->finishedAt(), 'actions')
        ->create();

    $neverExecuted = Campaign::factory()->enabled()->create();

    $records = collect([$executed, $neverExecuted]);

    // Establish starting state: both enabled and not archived.
    foreach ($records as $record) {
        expect($record->enabled)->toBeTrue()
            ->and($record->archived_at)->toBeNull();
    }

    livewire(ListCampaigns::class)
        ->selectTableRecords($records->pluck('id')->all())
        ->callAction(TestAction::make('archive')->table()->bulk())
        ->assertNotified();

    foreach ($records as $record) {
        // Still present — archive-only never deletes, even the never-executed one.
        assertModelExists($record);

        $record->refresh();

        expect($record->archived_at)->not->toBeNull()
            ->and($record->enabled)->toBeFalse();
    }
});

it('shows the disable and archive confirmation copy with the selected count', function () {
    asSuperAdmin();

    $campaigns = Campaign::factory()->count(2)->create();

    livewire(ListCampaigns::class)
        ->mountTableBulkAction('archive', $campaigns->pluck('id')->all())
        ->assertMountedActionModalSee('This action will disable and archive 2 selected campaign(s).');
});

it('does not render per-row view, edit, or delete actions', function () {
    asSuperAdmin();

    $campaign = Campaign::factory()->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([$campaign])
        ->assertTableActionDoesNotExist('view')
        ->assertTableActionDoesNotExist('edit')
        ->assertTableActionDoesNotExist('delete');
});

it('links each row to the campaign view page', function () {
    asSuperAdmin();

    $campaign = Campaign::factory()->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords([$campaign])
        ->assertSee(CampaignResource::getUrl('view', ['record' => $campaign]));
});

it('hides the archive bulk action from users without delete permission', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('campaign.view-any');

    actingAs($user);

    $campaigns = Campaign::factory()->count(2)->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords($campaigns)
        ->assertTableBulkActionHidden('archive');

    $user->givePermissionTo('campaign.*.delete');

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords($campaigns)
        ->assertTableBulkActionVisible('archive');
});
