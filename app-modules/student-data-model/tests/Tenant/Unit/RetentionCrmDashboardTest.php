<?php

use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentsActionCenterWidget;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentStats;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('renders all students correctly in the retention dashboard for the All tab', function () {
    $allStudents = Student::factory()->has(
        Task::factory()->state(['status' => TaskStatus::Pending]),
        'tasks'
    )->count(2)->create();

    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $studentsWithSubscription = Student::factory()->has(
        Subscription::factory()->state([
            'user_id' => $user->getKey(),
        ]),
        'subscriptions'
    )->count(2)->create();

    $careTeamRole = CareTeamRole::factory()->create();

    $studentsWithCareTeam = Student::factory()
        ->hasAttached(
            $user,
            ['care_team_role_id' => $careTeamRole->getKey()],
            'careTeam'
        )
        ->count(1)
        ->create();

    $statsWidget = new StudentStats();
    $statsWidget->activeTab = ActionCenterTab::All->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[3];
    expect($openTasksStat->getValue())->toEqual($allStudents->count());

    livewire(StudentsActionCenterWidget::class, ['activeTab' => ActionCenterTab::All->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords(
            $allStudents
                ->merge($studentsWithSubscription)
                ->merge($studentsWithCareTeam)
        );
});

it('renders subscribed students correctly in the retention dashboard for the Subscribed tab', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $studentsWithSubscription = Student::factory()
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

    $statsWidget = new StudentStats();
    $statsWidget->activeTab = ActionCenterTab::Subscribed->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[3];
    expect($openTasksStat->getValue())->toEqual($studentsWithSubscription->count());

    livewire(StudentsActionCenterWidget::class, ['activeTab' => ActionCenterTab::Subscribed->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($studentsWithSubscription);
});

it('renders care team students correctly in the retention dashboard for the Care Team tab', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $careTeamRole = CareTeamRole::factory()->create();

    $studentsWithCareTeam = Student::factory()
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

    $statsWidget = new StudentStats();
    $statsWidget->activeTab = ActionCenterTab::CareTeam->value;

    $stats = $statsWidget->getStats();

    $openTasksStat = $stats[3];
    expect($openTasksStat->getValue())->toEqual($studentsWithCareTeam->count());

    livewire(StudentsActionCenterWidget::class, ['activeTab' => ActionCenterTab::CareTeam->value])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($studentsWithCareTeam);
});
