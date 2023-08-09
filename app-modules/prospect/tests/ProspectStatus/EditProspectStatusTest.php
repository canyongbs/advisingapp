<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;
use Assist\Prospect\Models\ProspectStatus;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Assist\Prospect\Tests\ProspectStatus\RequestFactories\EditProspectStatusRequestFactory;

test('A successful action on the EditProspectStatus page', function () {
    $prospectStatus = ProspectStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditProspectStatusRequestFactory::new()->create();

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectStatus->name,
            'color' => $prospectStatus->color,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $prospectStatus->fresh()->name);
    assertEquals($editRequest['color'], $prospectStatus->fresh()->color);
});

test('EditProspectStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $prospectStatus = ProspectStatus::factory()->create();

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectStatus->name,
            'color' => $prospectStatus->color,
        ])
        ->fillForm(EditProspectStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ProspectStatus::class, $prospectStatus->toArray());
})->with(
    [
        'name missing' => [EditProspectStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditProspectStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [EditProspectStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [EditProspectStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('EditProspectStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectStatus = ProspectStatus::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus,
            ])
        )->assertForbidden();

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('prospect_status.view-any');
    $user->givePermissionTo('prospect_status.*.update');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditProspectStatusRequestFactory::new()->create());

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $prospectStatus->fresh()->name);
    assertEquals($request['color'], $prospectStatus->fresh()->color);
});
