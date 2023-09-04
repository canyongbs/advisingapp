<?php

use App\Models\User;
use Illuminate\Validation\Rules\Enum;
use Assist\Case\Models\ServiceRequestStatus;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource;
use Assist\Case\Tests\RequestFactories\EditCaseItemStatusRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

test('A successful action on the EditCaseItemStatus page', function () {
    $caseItemStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $caseItemStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCaseItemStatusRequestFactory::new()->create();

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

test('EditCaseItemStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $caseItemStatus = ServiceRequestStatus::factory()->create();

    livewire(CaseItemStatusResource\Pages\EditCaseItemStatus::class, [
        'record' => $caseItemStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemStatus->name,
            'color' => $caseItemStatus->color,
        ])
        ->fillForm(EditCaseItemStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestStatus::class, $caseItemStatus->toArray());
})->with(
    [
        'name missing' => [EditCaseItemStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditCaseItemStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [EditCaseItemStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [EditCaseItemStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('EditCaseItemStatus is gated with proper access control', function () {
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

    $request = collect(EditCaseItemStatusRequestFactory::new()->create());

    livewire(CaseItemStatusResource\Pages\EditCaseItemStatus::class, [
        'record' => $caseItemStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $caseItemStatus->fresh()->name);
});
