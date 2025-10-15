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

use AdvisingApp\Project\Filament\Resources\Projects\Pages\ListProjects;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ListProjects::getUrl())
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    get(ListProjects::getUrl())
        ->assertSuccessful();
});

it('can list records', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $records = Project::factory()->for($user)->count(5)->create();

    livewire(ListProjects::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

it('does not list projects to unauthorized manager users', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('project.create');

    $authorizedProjects = Project::factory()->count(5)->create();

    $authorizedProjects->each(fn ($project) => $project->managerUsers()->attach($user));

    $unauthorizedProjects = Project::factory()->count(5)->create();

    actingAs($user);

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($authorizedProjects)
        ->assertCanNotSeeTableRecords($unauthorizedProjects);
});

it('does not list projects to unauthorized manager teams', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('project.create');

    $authorizedTeam = Team::factory()->create();

    $user->team()->associate($authorizedTeam);
    $user->save();

    $user->refresh();

    $authorizedProjects = Project::factory()->count(5)->create();

    $authorizedProjects->each(fn ($project) => $project->managerTeams()->attach($authorizedTeam));

    $unauthorizedProjects = Project::factory()->count(5)->create();

    actingAs($user);

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($authorizedProjects)
        ->assertCanNotSeeTableRecords($unauthorizedProjects);
});

it('does not list projects to unauthorized auditor users', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('project.create');

    $authorizedProjects = Project::factory()->count(5)->create();

    $authorizedProjects->each(fn ($project) => $project->auditorUsers()->attach($user));

    $unauthorizedProjects = Project::factory()->count(5)->create();

    actingAs($user);

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($authorizedProjects)
        ->assertCanNotSeeTableRecords($unauthorizedProjects);
});

it('does not list projects to unauthorized auditor teams', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('project.create');

    $authorizedTeam = Team::factory()->create();

    $user->team()->associate($authorizedTeam);

    $user->save();
    $user->refresh();

    $authorizedProjects = Project::factory()->count(5)->create();

    $authorizedProjects->each(fn ($project) => $project->auditorTeams()->attach($authorizedTeam));

    $unauthorizedProjects = Project::factory()->count(5)->create();

    actingAs($user);

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($authorizedProjects)
        ->assertCanNotSeeTableRecords($unauthorizedProjects);
});
