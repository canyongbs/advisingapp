<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Scopes\ConfidentialTaskScope;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('is applied as a global scope to the task model', function () {
    Task::bootHasGlobalScopes();

    expect(Task::hasGlobalScope(ConfidentialTaskScope::class))->toBeTrue();
});

it('can be accessed by users when not confidential', function() {
  $user = User::factory()->licensed(LicenseType::cases())->create();

  actingAs($user);

  $confidentialTasks = Task::factory()->count(10)->concerningStudent(Student::factory()->create())->create(['is_confidential' => true]);
  $nonConfidentialTasks = Task::factory()->count(10)->concerningStudent(Student::factory()->create())->create(['is_confidential' => false]);

  $tasks = Task::query()->get();
  expect($tasks)->toHaveCount(10);

  expect($tasks->pluck('id'))
    ->toContain(...$nonConfidentialTasks->pluck('id'));
  
  expect($tasks->pluck('id'))
    ->not->toContain(...$confidentialTasks->pluck('id'));
});

it('can be accessed when confidential by users who created it or super admins', function() {
  $user = User::factory()->licensed(LicenseType::cases())->create();

  actingAs($user);

  $userTasks = Task::factory()->count(10)->concerningStudent(Student::factory()->create())->create([
    'is_confidential' => true,
    'created_by' => $user,
  ]);

  $tasks = Task::query()->get();

  expect($tasks->pluck('id'))
    ->toContain(...$userTasks->pluck('id'));

    asSuperAdmin();
  
    expect($tasks->pluck('id'))
    ->toContain(...$userTasks->pluck('id'));
});

it('can be accessed when confidential by users on a team with access', function() {
  $team = Team::factory()->create();
  $user = User::factory()->licensed(LicenseType::cases())->create();
  $user->team()->associate($team)->save();

  actingAs($user);

  $teamTasks = Task::factory()
    ->hasAttached($team, [], 'confidentialAccessTeams')
    ->count(10)
    ->concerningStudent(Student::factory()->create())
    ->create(['is_confidential' => true]);
  
  $otherTeamTasks = Task::factory()
    ->hasAttached(Team::factory()->create(), [], 'confidentialAccessTeams')
    ->count(10)
    ->concerningStudent(Student::factory()->create())
    ->create(['is_confidential' => true]);

  $tasks = Task::query()->get();
  expect($tasks)->toHaveCount(10);

  expect($tasks->pluck('id'))
    ->toContain(...$teamTasks->pluck('id'));
  
  expect($tasks->pluck('id'))
    ->not->toContain(...$otherTeamTasks->pluck('id'));
});
