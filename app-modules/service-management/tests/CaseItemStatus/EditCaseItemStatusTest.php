<?php

use App\Models\User;
use Illuminate\Validation\Rules\Enum;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestStatusRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

test('A successful action on the EditServiceRequestStatus page', function () {
    $caseItemStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $caseItemStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestStatusRequestFactory::new()->create();

    livewire(CaseItemStatusResource\Pages\EditCaseItemStatus::class, [
        'record' => $caseItemStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemStatus->name,
            'color' => $caseItemStatus->color,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $caseItemStatus->fresh()->name);
    assertEquals($editRequest['color'], $caseItemStatus->fresh()->color);
});

test('EditServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $caseItemStatus = ServiceRequestStatus::factory()->create();

    livewire(CaseItemStatusResource\Pages\EditCaseItemStatus::class, [
        'record' => $caseItemStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemStatus->name,
            'color' => $caseItemStatus->color,
        ])
        ->fillForm(EditServiceRequestStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestStatus::class, $caseItemStatus->toArray());
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

    $caseItemStatus = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $caseItemStatus,
            ])
        )->assertForbidden();

    livewire(CaseItemStatusResource\Pages\EditCaseItemStatus::class, [
        'record' => $caseItemStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('case_item_status.view-any');
    $user->givePermissionTo('case_item_status.*.update');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $caseItemStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestStatusRequestFactory::new()->create());

    livewire(CaseItemStatusResource\Pages\EditCaseItemStatus::class, [
        'record' => $caseItemStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $caseItemStatus->fresh()->name);
});
