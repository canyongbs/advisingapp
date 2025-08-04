<?php

use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\CreateProject;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Tests\Tenant\Filament\Resources\ProjectResource\RequestFactory\CreateProjectRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(CreateProject::getUrl())
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.create');

    $user->refresh();

    actingAs($user);

    get(CreateProject::getUrl())
        ->assertSuccessful();
});

it('validates the inputs', function (CreateProjectRequestFactory $data, array $errors) {
    asSuperAdmin();

    $request = CreateProjectRequestFactory::new($data)->create();

    $user = User::factory()->create();

    Project::factory()->for($user, 'createdBy')->create(['name' => 'Test Project']);

    livewire(CreateProject::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseCount(Project::class, 1);
})->with(
    [
        'name required' => [
            CreateProjectRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            CreateProjectRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            CreateProjectRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'name unique' => [
            CreateProjectRequestFactory::new()->state(['name' => 'Test Project']),
            ['name' => 'unique'],
        ],
        'description string' => [
            CreateProjectRequestFactory::new()->state(['description' => 1]),
            ['description' => 'string'],
        ],
        'description max' => [
            CreateProjectRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
    ]
);

it('can create a record', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.create');

    actingAs($user);

    $request = CreateProjectRequestFactory::new()->create();

    livewire(CreateProject::class)
        ->fillForm($request)
        ->call('create')
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
