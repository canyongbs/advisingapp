<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateServiceRequestStatusRequestFactory;

test('A successful action on the CreateServiceRequestStatus page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateServiceRequestStatusRequestFactory::new()->create();

    livewire(ServiceRequestStatusResource\Pages\CreateServiceRequestStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request);
});

test('CreateServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ServiceRequestStatusResource\Pages\CreateServiceRequestStatus::class)
        ->fillForm(CreateServiceRequestStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestStatus::all());
})->with(
    [
        'name missing' => [CreateServiceRequestStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateServiceRequestStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateServiceRequestStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateServiceRequestStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('CreateServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(ServiceRequestStatusResource\Pages\CreateServiceRequestStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request_status.view-any');
    $user->givePermissionTo('service_request_status.create');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestStatusRequestFactory::new()->create());

    livewire(ServiceRequestStatusResource\Pages\CreateServiceRequestStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request->toArray());
});
