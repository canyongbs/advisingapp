<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestStatusRequestFactory;

test('A successful action on the EditServiceRequestStatus page', function () {
    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestStatusRequestFactory::new()->create();

    livewire(ServiceRequestStatusResource\Pages\EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $serviceRequestStatus->classification->value,
            'name' => $serviceRequestStatus->name,
            'color' => $serviceRequestStatus->color->value,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $serviceRequestStatus->fresh()->name);
    assertEquals($editRequest['classification'], $serviceRequestStatus->fresh()->classification);
    assertEquals($editRequest['color'], $serviceRequestStatus->fresh()->color);
});

test('EditServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    livewire(ServiceRequestStatusResource\Pages\EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $serviceRequestStatus->classification->value,
            'name' => $serviceRequestStatus->name,
            'color' => $serviceRequestStatus->color->value,
        ])
        ->fillForm(EditServiceRequestStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestStatus::class, $serviceRequestStatus->toArray());
})->with(
    [
        'name missing' => [EditServiceRequestStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditServiceRequestStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [EditServiceRequestStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [EditServiceRequestStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('EditServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertForbidden();

    livewire(ServiceRequestStatusResource\Pages\EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request_status.view-any');
    $user->givePermissionTo('service_request_status.*.update');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestStatusRequestFactory::new()->create());

    livewire(ServiceRequestStatusResource\Pages\EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestStatus->fresh()->name);
});
