<?php

use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\ListProjects;
use AdvisingApp\Project\Models\Project;
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

    $records = Project::factory()->count(5)->create();

    livewire(ListProjects::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});
