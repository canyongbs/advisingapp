<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionUsersTable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Livewire\livewire;

it('can see student interaction users table', function () {
    $team = Team::factory()->create();

    $user1 = User::factory()->create();

    $user2 = User::factory()->for($team, 'team')->create();

    $student = Student::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($student, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($student, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    $firstUserInteractionAt = $user1->interactions->sortBy('created_at')->first();
    $firstUserInteractionAtDate = $firstUserInteractionAt ? $firstUserInteractionAt->created_at->format('M d, Y') : null;

    $secondUserInteractionAt = $user2->interactions->sortBy('created_at')->first();
    $secondUserInteractionAtDate = $secondUserInteractionAt ? $secondUserInteractionAt->created_at->format('M d, Y') : null;

    $firstUserMostRecentInteractionAt = $user1->interactions->sortByDesc('created_at')->first();
    $firstUserMostRecentInteractionAtDate = $firstUserMostRecentInteractionAt ? $firstUserMostRecentInteractionAt->created_at->format('M d, Y') : null;

    $secondUserMostRecentInteractionAt = $user2->interactions->sortByDesc('created_at')->first();
    $secondUserMostRecentInteractionAtDate = $secondUserMostRecentInteractionAt ? $secondUserMostRecentInteractionAt->created_at->format('M d, Y') : null;

    $totalInteractionUser1 = $user1->interactions->count();
    $totalInteractionUser2 = $user2->interactions->count();

    $totalInteractions = $totalInteractionUser1 + $totalInteractionUser2;

    $totalPerUser1 = $totalInteractions > 0 ? round(($user1->interactions->count() / $totalInteractions) * 100) : 0;
    $totalPerUser2 = $totalInteractions > 0 ? round(($user2->interactions->count() / $totalInteractions) * 100) : 0;

    $durationsUser1 = $user1->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), true);
    })->filter();

    $avgUser1 = round($durationsUser1->avg());

    $durationsUser2 = $user2->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), true);
    })->filter();

    $avgUser2 = round($durationsUser2->avg());

    $tableRecords = collect([$user1, $user2]);

    livewire(StudentInteractionUsersTable::class, ['cacheTag' => 'report-student-interaction'])
        ->assertCanSeeTableRecords($tableRecords)
        ->assertTableColumnStateSet('name', $user1->name, $user1)
        ->assertTableColumnStateSet('name', $user2->name, $user2)
        ->assertTableColumnStateSet('first_interaction_at', $firstUserInteractionAtDate, $user1)
        ->assertTableColumnStateSet('first_interaction_at', $secondUserInteractionAtDate, $user2)
        ->assertTableColumnStateSet('most_recent_interaction_at', $firstUserMostRecentInteractionAtDate, $user1)
        ->assertTableColumnStateSet('most_recent_interaction_at', $secondUserMostRecentInteractionAtDate, $user2)
        ->assertTableColumnStateSet('total_interactions', $totalInteractionUser1, $user1)
        ->assertTableColumnStateSet('total_interactions', $totalInteractionUser2, $user2)
        ->assertTableColumnStateSet('total_interactions_percent', "{$totalPerUser1}%", $user1)
        ->assertTableColumnStateSet('total_interactions_percent', "{$totalPerUser2}%", $user2)
        ->assertTableColumnStateSet('avg_interaction_duration', "{$avgUser1} Min.", $user1)
        ->assertTableColumnStateSet('avg_interaction_duration', "{$avgUser2} Min.", $user2);
});

it('can filter users by name', function () {
    $team = Team::factory()->create([
        'name' => 'Test Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $student = Student::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($student, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($student, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(StudentInteractionUsersTable::class, ['cacheTag' => 'report-student-interaction'])
        ->filterTable('name', [
            'name' => 'Super Admin',
        ])
        ->assertCanSeeTableRecords(collect([$user1]))
        ->assertCanNotSeeTableRecords(collect([$user2]));
});

it('can filter users by job title', function () {
    $team = Team::factory()->create([
        'name' => 'Test Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $student = Student::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($student, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($student, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(StudentInteractionUsersTable::class, ['cacheTag' => 'report-student-interaction'])
        ->filterTable('job_title', [
            'job_title' => 'Computer Operator',
        ])
        ->assertCanSeeTableRecords(collect([$user1]))
        ->assertCanNotSeeTableRecords(collect([$user2]));
});

it('can filter users by team', function () {
    $team = Team::factory()->create([
        'name' => 'Interaction Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $student = Student::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($student, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($student, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(StudentInteractionUsersTable::class, ['cacheTag' => 'report-student-interaction'])
        ->filterTable('team', [
            'team' => $team->getKey(),
        ])
        ->assertCanSeeTableRecords(collect([$user2]))
        ->assertCanNotSeeTableRecords(collect([$user1]));
});
