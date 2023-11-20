<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\assertDatabaseHas;

use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestUpdateRequestFactory;

test('A successful action on the EditServiceRequestUpdate page', function () {
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditServiceRequestUpdateRequestFactory::new()->create());

    livewire(ServiceRequestUpdateResource\Pages\EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestUpdate::class, $request->except('service_request_id')->toArray());

    expect(ServiceRequestUpdate::first()->serviceRequest->id)
        ->toEqual($request->get('service_request_id'));
});

test('EditServiceRequestUpdate requires valid data', function ($data, $errors) {
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    asSuperAdmin();

    livewire(ServiceRequestUpdateResource\Pages\EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->fillForm(EditServiceRequestUpdateRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    unset($serviceRequestUpdate->serviceRequest);

    assertDatabaseHas(ServiceRequestUpdate::class, $serviceRequestUpdate->toArray());

    expect(ServiceRequestUpdate::first()->serviceRequest->id)
        ->toEqual($serviceRequestUpdate->serviceRequest->id);
})->with(
    [
        'service_request missing' => [EditServiceRequestUpdateRequestFactory::new()->state(['service_request_id' => null]), ['service_request_id' => 'required']],
        'service_request not existing service_request id' => [EditServiceRequestUpdateRequestFactory::new()->state(['service_request_id' => fake()->uuid()]), ['service_request_id' => 'exists']],
        'update missing' => [EditServiceRequestUpdateRequestFactory::new()->state(['update' => null]), ['update' => 'required']],
        'update is not a string' => [EditServiceRequestUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'direction missing' => [EditServiceRequestUpdateRequestFactory::new()->state(['direction' => null]), ['direction' => 'required']],
        'direction not a valid enum' => [EditServiceRequestUpdateRequestFactory::new()->state(['direction' => 'invalid']), ['direction' => Enum::class]],
        'internal not a boolean' => [EditServiceRequestUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);

// Permission Tests

test('EditServiceRequestUpdate is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertForbidden();

    livewire(ServiceRequestUpdateResource\Pages\EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.update');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestUpdateRequestFactory::new()->create());

    livewire(ServiceRequestUpdateResource\Pages\EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestUpdate::class, $request->except('service_request_id')->toArray());

    expect(ServiceRequestUpdate::first()->serviceRequest->id)
        ->toEqual($request->get('service_request_id'));
});
