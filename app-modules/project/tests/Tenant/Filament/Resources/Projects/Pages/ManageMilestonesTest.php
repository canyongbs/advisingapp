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

use AdvisingApp\Project\Database\Factories\ProjectMilestoneFactory;
use AdvisingApp\Project\Database\Seeders\ProjectMilestoneStatusSeeder;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageMilestones;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Models\ProjectMilestone;
use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(ManageMilestones::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->refresh();

    get(ManageMilestones::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can list milestones', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    ProjectMilestone::factory()->count(5)->for($project)->create();

    livewire(ManageMilestones::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($project->milestones);
});

it('can validate create milestone inputs', function (array $data, mixed $errors) {
    asSuperAdmin();

    $project = Project::factory()->create();
    $milestone = ProjectMilestone::factory()->make($data);

    livewire(ManageMilestones::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('create', data: $milestone->toArray())
        ->assertHasTableActionErrors([$errors]);

    assertDatabaseMissing(
        ProjectMilestone::class,
        $milestone->toArray()
    );
})->with([
    '`title` is required' => [['title' => null], 'title', 'The title field is required.'],
    '`title` is max 255 characters' => [['title' => str_repeat('a', 256)], 'title', 'The title may not be greater than 255 characters.'],
    '`description` is required' => [['description' => null], 'description', 'The description field is required.'],
    '`description` is max 65535 characters' => [['description' => str_repeat('a', 65536)], 'description', 'The description may not be greater than 65535 characters.'],
    '`status_id` is required' => [['status_id' => null], 'status_id', 'The status field is required.'],
]);

it('can create milestones', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $projectMilestone = ProjectMilestoneFactory::new()->make([
        'project_id' => $project->id,
    ]);

    livewire(ManageMilestones::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('create', data: $projectMilestone->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, ProjectMilestone::all());
});

it('can edit milestones', function () {
    seed(ProjectMilestoneStatusSeeder::class);
    asSuperAdmin();

    $project = Project::factory()->create();

    $plannedStatus = ProjectMilestoneStatus::where('name', 'Planned')->first();
    $delayedStatus = ProjectMilestoneStatus::where('name', 'Delayed')->first();

    $milestone = ProjectMilestone::factory()->state([
        'project_id' => $project->id,
        'description' => 'Test project milestone',
        'status_id' => $plannedStatus->id,
        'created_by_id' => auth()->id(),
    ])->create();

    $request = ProjectMilestone::factory()->make([
        'project_id' => $project->id,
        'description' => 'Changed Test project milestone',
        'status_id' => $delayedStatus->id,
        'created_by_id' => auth()->id(),
    ]);

    livewire(ManageMilestones::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('edit', record: $milestone->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        ProjectMilestone::class,
        $request->toArray()
    );
});
