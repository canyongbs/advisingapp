<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestTypeRequestFactory;

test('A successful action on the EditServiceRequestType page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestTypeRequestFactory::new()->create();

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $serviceRequestType->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $serviceRequestType->fresh()->name);
});

test('EditServiceRequestType requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $serviceRequestType->name,
        ])
        ->fillForm(EditServiceRequestTypeRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestType::class, $serviceRequestType->toArray());
})->with(
    [
        'name missing' => [EditServiceRequestTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditServiceRequestTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequestType is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $serviceRequestType,
            ])
        )->assertForbidden();

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');
    $user->givePermissionTo('service_request_type.*.update');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $serviceRequestType,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestTypeRequestFactory::new()->create());

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestType->fresh()->name);
});
