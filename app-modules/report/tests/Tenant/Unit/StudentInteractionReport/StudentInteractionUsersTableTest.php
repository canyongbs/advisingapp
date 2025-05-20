<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionUsersTable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Livewire\livewire;

it('can see student interaction users table', function () {
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

    $totalPerUser1 = round((5 / 15) * 100);
    $totalPerUser2 = round((10 / 15) * 100);

    $durationsUser1 = $user1->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), Carbon::DIFF_ABSOLUTE);
    })->filter();

    $avgUser1 = round($durationsUser1->avg());

    $durationsUser2 = $user2->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), Carbon::DIFF_ABSOLUTE);
    })->filter();

    $avgUser2 = round($durationsUser2->avg());

    livewire(StudentInteractionUsersTable::class, ['cacheTag' => 'report-student-interaction'])
        ->assertSee([
            'Super Admin',
            'Computer Operator',
            'Canyon GBS',
            'CEO',
            'Test Team',
            '5',
            '10',
            now()->format('M d, Y'),
            $totalPerUser1 . '%',
            $totalPerUser2 . '%',
            $avgUser1 . ' Min.',
            $avgUser2 . ' Min.',
        ]);
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
        ->assertSee([
            'Super Admin',
        ])
        ->assertDontSee([
            'Canyon GBS',
        ]);
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
        ->assertSee([
            'Computer Operator',
        ])
        ->assertDontSee([
            'CEO',
        ]);
});

it('can filter users by team', function () {
    $team = Team::factory()->create([
        'name' => 'Interaction Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'user1',
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
        ->assertSee([
            'Team: Interaction Team',
            'Canyon GBS',
        ]);
    /*->assertDontSee([
        'user1',
    ]);*/
});
