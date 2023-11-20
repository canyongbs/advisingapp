<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Prospect\Models\ProspectSource;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\Prospect\Filament\Resources\ProspectSourceResource;
use Assist\Prospect\Tests\ProspectSource\RequestFactories\EditProspectSourceRequestFactory;

test('A successful action on the EditProspectSource page', function () {
    $prospectSource = ProspectSource::factory()->create();

    asSuperAdmin()
        ->get(
            ProspectSourceResource::getUrl('edit', [
                'record' => $prospectSource->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditProspectSourceRequestFactory::new()->create();

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectSource->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $prospectSource->fresh()->name);
});

test('EditProspectSource requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $prospectSource = ProspectSource::factory()->create();

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectSource->name,
        ])
        ->fillForm(EditProspectSourceRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ProspectSource::class, $prospectSource->toArray());
})->with(
    [
        'name missing' => [EditProspectSourceRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditProspectSourceRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditProspectSource is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectSource = ProspectSource::factory()->create();

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('edit', [
                'record' => $prospectSource,
            ])
        )->assertForbidden();

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('prospect_source.view-any');
    $user->givePermissionTo('prospect_source.*.update');

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('edit', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();

    $request = collect(EditProspectSourceRequestFactory::new()->create());

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $prospectSource->fresh()->name);
});
