<?php

use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Prospect\Filament\Widgets\ProspectsActionCenterWidget;
use AdvisingApp\Prospect\Filament\Widgets\ProspectStats;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Filament\Widgets\ProspectGrowthChart;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('renders all prospects correctly in the recruitment dashboard for the All tab', function () {
    $allProspects = Prospect::factory()->has(
        Task::factory()->state(['status' => TaskStatus::Pending]),
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

    $openTasksStat = $stats[3];
    expect($openTasksStat->getValue())->toEqual($allProspects->count());

    livewire(ProspectsActionCenterWidget::class, ['activeTab' => ActionCenterTab::All->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($allRelevantProspects);

    $chartWidget = new ProspectGrowthChart();
    $chartWidget->activeTab = ActionCenterTab::All->value;

    expect($chartWidget->getData())->toMatchSnapshot();
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
            Task::factory()->state(['status' => TaskStatus::Pending]),
            'tasks'
        )->count(2)->create();

    $statsWidget = new ProspectStats();
    $statsWidget->activeTab = ActionCenterTab::Subscribed->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[3];
    expect($openTasksStat->getValue())->toEqual($prospectsWithSubscription->count());

    livewire(ProspectsActionCenterWidget::class, ['activeTab' => ActionCenterTab::Subscribed->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($prospectsWithSubscription);

    $chartWidget = new ProspectGrowthChart();
    $chartWidget->activeTab = ActionCenterTab::Subscribed->value;

    expect($chartWidget->getData())->toMatchSnapshot();
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
            Task::factory()->state(['status' => TaskStatus::Pending]),
            'tasks'
        )
        ->count(2)
        ->create();

    $statsWidget = new ProspectStats();
    $statsWidget->activeTab = ActionCenterTab::CareTeam->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[3];
    expect($openTasksStat->getValue())->toEqual($prospectsWithCareTeam->count());

    livewire(ProspectsActionCenterWidget::class, ['activeTab' => ActionCenterTab::CareTeam->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($prospectsWithCareTeam);

    $chartWidget = new ProspectGrowthChart();
    $chartWidget->activeTab = ActionCenterTab::CareTeam->value;

    expect($chartWidget->getData())->toMatchSnapshot();
});
