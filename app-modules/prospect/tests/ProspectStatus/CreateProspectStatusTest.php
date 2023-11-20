<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;
use Assist\Prospect\Models\ProspectStatus;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Assist\Prospect\Tests\ProspectStatus\RequestFactories\CreateProspectStatusRequestFactory;

test('A successful action on the CreateProspectStatus page', function () {
    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateProspectStatusRequestFactory::new()->create();

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectStatus::all());

    assertDatabaseHas(ProspectStatus::class, $request);
});

test('CreateProspectStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->fillForm(CreateProspectStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ProspectStatus::all());
})->with(
    [
        'name missing' => [CreateProspectStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateProspectStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateProspectStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateProspectStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('CreateProspectStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('prospect_status.view-any');
    $user->givePermissionTo('prospect_status.create');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateProspectStatusRequestFactory::new()->create());

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectStatus::all());

    assertDatabaseHas(ProspectStatus::class, $request->toArray());
});
