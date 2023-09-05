<?php

use App\Models\User;
use Illuminate\Validation\Rules\Enum;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateServiceRequestStatusRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

test('A successful action on the CreateServiceRequestStatus page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateServiceRequestStatusRequestFactory::new()->create();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request);
});

test('CreateServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
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

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('case_item_status.view-any');
    $user->givePermissionTo('case_item_status.create');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestStatusRequestFactory::new()->create());

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request->toArray());
});
