<?php

use App\Models\User;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestTypeRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Pest\Laravel\assertDatabaseHas;

test('A successful action on the EditServiceRequestType page', function () {
    $caseItemType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $caseItemType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestTypeRequestFactory::new()->create();

    livewire(CaseItemTypeResource\Pages\EditCaseItemType::class, [
        'record' => $caseItemType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemType->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $caseItemType->fresh()->name);
});

test('EditServiceRequestType requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $caseItemType = ServiceRequestType::factory()->create();

    livewire(CaseItemTypeResource\Pages\EditCaseItemType::class, [
        'record' => $caseItemType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemType->name,
        ])
        ->fillForm(EditServiceRequestTypeRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestType::class, $caseItemType->toArray());
})->with(
    [
        'name missing' => [EditServiceRequestTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditServiceRequestTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequestType is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseItemType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $caseItemType,
            ])
        )->assertForbidden();

    livewire(CaseItemTypeResource\Pages\EditCaseItemType::class, [
        'record' => $caseItemType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('case_item_type.view-any');
    $user->givePermissionTo('case_item_type.*.update');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $caseItemType,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestTypeRequestFactory::new()->create());

    livewire(CaseItemTypeResource\Pages\EditCaseItemType::class, [
        'record' => $caseItemType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $caseItemType->fresh()->name);
});
