<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateCaseItemTypeRequestFactory;

test('A successful action on the CreateServiceRequestType page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('create')
        )
        ->assertSuccessful();

    $editRequest = CreateCaseItemTypeRequestFactory::new()->create();

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->fillForm($editRequest)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestType::all());

    assertDatabaseHas(ServiceRequestType::class, $editRequest);
});

test('CreateServiceRequestType requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->fillForm(CreateCaseItemTypeRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestType::all());
})->with(
    [
        'name missing' => [CreateCaseItemTypeRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateCaseItemTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('CreateServiceRequestType is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('create')
        )->assertForbidden();

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->assertForbidden();

    $user->givePermissionTo('case_item_type.view-any');
    $user->givePermissionTo('case_item_type.create');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseItemTypeRequestFactory::new()->create());

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestType::all());

    assertDatabaseHas(ServiceRequestType::class, $request->toArray());
});
