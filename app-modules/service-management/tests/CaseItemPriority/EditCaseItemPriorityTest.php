<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditCaseItemPriorityRequestFactory;

test('A successful action on the EditServiceRequestPriority page', function () {
    $caseItemPriority = ServiceRequestPriority::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestPriorityResource::getUrl('edit', [
                'record' => $caseItemPriority->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCaseItemPriorityRequestFactory::new()->create();

    livewire(CaseItemPriorityResource\Pages\EditCaseItemPriority::class, [
        'record' => $caseItemPriority->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemPriority->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $caseItemPriority->fresh()->name);
});

test('EditServiceRequestPriority requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $caseItemPriority = ServiceRequestPriority::factory()->create();

    livewire(CaseItemPriorityResource\Pages\EditCaseItemPriority::class, [
        'record' => $caseItemPriority->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemPriority->name,
        ])
        ->fillForm(EditCaseItemPriorityRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestPriority::class, $caseItemPriority->toArray());
})->with(
    [
        'name missing' => [EditCaseItemPriorityRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditCaseItemPriorityRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequestPriority is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseItemPriority = ServiceRequestPriority::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('edit', [
                'record' => $caseItemPriority,
            ])
        )->assertForbidden();

    livewire(CaseItemPriorityResource\Pages\EditCaseItemPriority::class, [
        'record' => $caseItemPriority->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('case_item_priority.view-any');
    $user->givePermissionTo('case_item_priority.*.update');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('edit', [
                'record' => $caseItemPriority,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseItemPriorityRequestFactory::new()->create());

    livewire(CaseItemPriorityResource\Pages\EditCaseItemPriority::class, [
        'record' => $caseItemPriority->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $caseItemPriority->fresh()->name);
});
