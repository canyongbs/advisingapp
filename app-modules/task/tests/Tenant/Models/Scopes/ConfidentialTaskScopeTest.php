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
use AdvisingApp\Project\Models\Project;
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

it('can be accessed by users when not confidential', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $confidentialTasks = Task::factory()->count(10)->concerningStudent(Student::factory()->create())->create(['is_confidential' => true]);
    $nonConfidentialTasks = Task::factory()->count(10)->concerningStudent(Student::factory()->create())->create(['is_confidential' => false]);

    $tasks = Task::query()->get();
    expect($tasks)->toHaveCount(10);

    expect($tasks->pluck('id'))->toContain(...$nonConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$confidentialTasks->pluck('id'));
});

it('can be accessed when confidential by users who created it', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $userTasks = Task::factory()->count(10)->concerningStudent(Student::factory()->create())->create([
        'is_confidential' => true,
        'created_by' => $user,
    ]);

    $tasks = Task::query()->get();

    expect($tasks->pluck('id'))->toContain(...$userTasks->pluck('id'));
});

it('can be accessed when confidential by users on a team with access', function () {
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

    expect($tasks->pluck('id'))->toContain(...$teamTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$otherTeamTasks->pluck('id'));
});

it('can be accessed when confidential by users who have created a project the task is associated with', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $project = Project::factory()->for($user, 'createdBy')->create();

    $confidentialTasks = Task::factory()->count(10)->for($project)->concerningStudent(Student::factory()->create())->create(['is_confidential' => true]);

    actingAs($user);

    $tasks = Task::query()->get();

    expect($tasks->pluck('id'))->toContain(...$confidentialTasks->pluck('id'));
});

it('can be accessed when confidential by super admins', function () {
    $confidentialTasks = Task::factory()->count(10)->concerningStudent(Student::factory()->create())->create(['is_confidential' => true]);

    asSuperAdmin();
    
    $tasks = Task::query()->get();

    expect($tasks->pluck('id'))->toContain(...$confidentialTasks->pluck('id'));
});
