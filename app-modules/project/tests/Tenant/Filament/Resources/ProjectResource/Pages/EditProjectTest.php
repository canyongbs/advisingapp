<?php

use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\EditProject;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Tests\Tenant\Filament\Resources\ProjectResource\RequestFactory\EditProjectRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.update');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('validates the inputs', function (EditProjectRequestFactory $data, array $errors) {
    asSuperAdmin();

    $user = User::factory()->create();

    $project = Project::factory()->for($user, 'createdBy')->create();

    Project::factory()->for($user, 'createdBy')->create(['name' => 'Test Project']);

    $request = EditProjectRequestFactory::new($data)->create();

    livewire(EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(
        Project::class,
        $request
    );
})->with(
    [
        'name required' => [
            EditProjectRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            EditProjectRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            EditProjectRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'name unique' => [
            EditProjectRequestFactory::new()->state(['name' => 'Test Project']),
            ['name' => 'unique'],
        ],
        'description string' => [
            EditProjectRequestFactory::new()->state(['description' => 1]),
            ['description' => 'string'],
        ],
        'description max' => [
            EditProjectRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
    ]
);

it('can edit a record', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.update');

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    $request = EditProjectRequestFactory::new()->create();

    livewire(EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    assertDatabaseCount(Project::class, 1);

    assertDatabaseHas(
        Project::class,
        [
            'name' => $request['name'],
            'description' => $request['description'],
        ]
    );
});
