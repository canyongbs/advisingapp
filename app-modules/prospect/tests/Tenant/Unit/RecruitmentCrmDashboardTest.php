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
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Prospect\Filament\Pages\RecruitmentCrmDashboard;
use AdvisingApp\Prospect\Filament\Widgets\ProspectsActionCenterWidget;
use AdvisingApp\Prospect\Filament\Widgets\ProspectStats;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('renders all prospects correctly in the recruitment dashboard for the All tab', function () {
    $allProspects = Prospect::factory()->has(
        Task::factory()->state(['status' => TaskStatus::Pending, 'is_confidential' => false]),
        'tasks'
    )->count(2)->create();

    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    $prospectsWithSubscription = Prospect::factory()->has(
        Subscription::factory()->state([
            'user_id' => $user->getKey(),
        ]),
        'subscriptions'
    )->count(2)->create();

    $careTeamRole = CareTeamRole::factory()->create();

    $prospectsWithCareTeam = Prospect::factory()
        ->hasAttached(
            $user,
            ['care_team_role_id' => $careTeamRole->getKey()],
            'careTeam'
        )
        ->count(1)
        ->create();

    $allRelevantProspects = $allProspects
        ->merge($prospectsWithSubscription)
        ->merge($prospectsWithCareTeam);

    $statsWidget = new ProspectStats();
    $statsWidget->activeTab = ActionCenterTab::All->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[2];
    expect($openTasksStat->getValue())->toEqual($allProspects->count());

    livewire(ProspectsActionCenterWidget::class, ['activeTab' => ActionCenterTab::All->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($allRelevantProspects);
});

it('renders subscribed prospects correctly in the recruitment dashboard for the Subscribed tab', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    $prospectsWithSubscription = Prospect::factory()
        ->has(
            Subscription::factory()->state([
                'user_id' => $user->getKey(),
            ]),
            'subscriptions'
        )
        ->has(
            Task::factory()->state(['status' => TaskStatus::Pending, 'is_confidential' => false]),
            'tasks'
        )->count(2)->create();

    $statsWidget = new ProspectStats();
    $statsWidget->activeTab = ActionCenterTab::Subscribed->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[2];
    expect($openTasksStat->getValue())->toEqual($prospectsWithSubscription->count());

    livewire(ProspectsActionCenterWidget::class, ['activeTab' => ActionCenterTab::Subscribed->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($prospectsWithSubscription);
});

it('renders care team prospects correctly in the recruitment dashboard for the Care Team tab', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    $careTeamRole = CareTeamRole::factory()->create();

    $prospectsWithCareTeam = Prospect::factory()
        ->hasAttached(
            $user,
            ['care_team_role_id' => $careTeamRole->getKey()],
            'careTeam'
        )
        ->has(
            Task::factory()->state(['status' => TaskStatus::Pending, 'is_confidential' => false]),
            'tasks'
        )
        ->count(2)
        ->create();

    $statsWidget = new ProspectStats();
    $statsWidget->activeTab = ActionCenterTab::CareTeam->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[2];
    expect($openTasksStat->getValue())->toEqual($prospectsWithCareTeam->count());

    livewire(ProspectsActionCenterWidget::class, ['activeTab' => ActionCenterTab::CareTeam->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($prospectsWithCareTeam);
});

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(RecruitmentCrmDashboard::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    get(RecruitmentCrmDashboard::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(RecruitmentCrmDashboard::getUrl())->assertSuccessful();
});