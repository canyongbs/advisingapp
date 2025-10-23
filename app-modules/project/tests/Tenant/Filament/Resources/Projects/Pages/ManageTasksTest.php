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
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageTasks;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();
    $project->createdBy()->associate($user);
    $project->save();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->revokePermissionTo('project.view-any');
    $user->revokePermissionTo('project.*.view');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    $user->refresh();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('shows the edit and delete actions with proper permissions', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();
    $project->createdBy()->associate($user);
    $project->save();

    $task = Task::factory()->for($project)->concerningStudent(Student::factory()->create())->create([
        'is_confidential' => false,
        'assigned_to' => $user->id,
        'created_by' => $user->id,
    ]);

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionHidden(EditAction::class)
        ->assertTableActionHidden(DeleteAction::class, $task)
        ->assertTableBulkActionHidden(DeleteBulkAction::class);

    $user->givePermissionTo('project.*.update');
    $user->givePermissionTo('task.*.update');
    $user->givePermissionTo('task.*.delete');

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionVisible(EditAction::class)
        ->assertTableActionVisible(DeleteAction::class, $task)
        ->assertTableBulkActionVisible(DeleteBulkAction::class);
});

it('can list tasks', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Task::factory()->count(5)->for($project)->concerningStudent(Student::factory()->create())->create(['is_confidential' => false]);

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($project->tasks);
});
