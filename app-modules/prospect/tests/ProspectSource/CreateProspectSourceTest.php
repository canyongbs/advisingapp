<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Prospect\Models\ProspectSource;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Prospect\Filament\Resources\ProspectSourceResource;
use Assist\Prospect\Tests\ProspectSource\RequestFactories\CreateProspectSourceRequestFactory;

test('A successful action on the CreateProspectSource page', function () {
    asSuperAdmin()
        ->get(
            ProspectSourceResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateProspectSourceRequestFactory::new()->create();

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectSource::all());

    assertDatabaseHas(ProspectSource::class, $request);
});

test('CreateProspectSource requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->fillForm(CreateProspectSourceRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ProspectSource::all());
})->with(
    [
        'name missing' => [CreateProspectSourceRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateProspectSourceRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('CreateProspectSource is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('create')
        )->assertForbidden();

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->assertForbidden();

    $user->givePermissionTo('prospect_source.view-any');
    $user->givePermissionTo('prospect_source.create');

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateProspectSourceRequestFactory::new()->create());

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectSource::all());

    assertDatabaseHas(ProspectSource::class, $request->toArray());
});
