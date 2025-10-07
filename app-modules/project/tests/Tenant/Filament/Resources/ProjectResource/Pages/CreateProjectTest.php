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

use AdvisingApp\Project\Filament\Resources\Projects\Pages\CreateProject;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Tests\Tenant\Filament\Resources\Projects\RequestFactory\CreateProjectRequestFactory;
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
