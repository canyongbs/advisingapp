<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission', function() {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();

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

it('shows the associate and dissociate actions with proper permissions', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionHidden(AssociateAction::class)
        ->assertTableActionHidden(DissociateAction::class)
        ->assertTableActionHidden(DissociateBulkAction::class);

    $user->givePermissionTo('project.*.update');

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionVisible(AssociateAction::class)
        ->assertTableActionVisible(DissociateAction::class)
        ->assertTableActionVisible(DissociateBulkAction::class);
});

it('can list tasks', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Task::factory()->count(5)->for($project)->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($project->tasks);
});

it('does not list tasks already associated with another project in task search', function () {
    asSuperAdmin();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    $task = Task::factory()->for($project1)->create();

    livewire(ManageTasks::class, [
        'record' => $project2->getRouteKey(),
    ])
        ->mountTableAction(AssociateAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) use ($task) {
            $options = $select->getSearchResults($task->title);

            return empty($options);
        })
        ->assertSuccessful();
});
