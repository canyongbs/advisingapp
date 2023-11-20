<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateServiceRequestPriorityRequestFactory;

test('A successful action on the CreateServiceRequestPriority page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestPriorityResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateServiceRequestPriorityRequestFactory::new()->create();

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestPriority::all());

    assertDatabaseHas(ServiceRequestPriority::class, $request);
});

test('CreateServiceRequestPriority requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->fillForm(CreateServiceRequestPriorityRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestPriority::all());
})->with(
    [
        'name missing' => [CreateServiceRequestPriorityRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateServiceRequestPriorityRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'order missing' => [CreateServiceRequestPriorityRequestFactory::new()->without('order'), ['order' => 'required']],
        'order not a number' => [CreateServiceRequestPriorityRequestFactory::new()->state(['order' => 'a']), ['order' => 'numeric']],
    ]
);

// Permission Tests

test('CreateServiceRequestPriority is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('create')
        )->assertForbidden();

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request_priority.view-any');
    $user->givePermissionTo('service_request_priority.create');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestPriorityRequestFactory::new()->create());

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestPriority::all());

    assertDatabaseHas(ServiceRequestPriority::class, $request->toArray());
});
