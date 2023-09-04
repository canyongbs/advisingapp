<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Filament\Resources\ServiceRequestResource;
use Assist\Case\Tests\RequestFactories\EditCaseItemRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;

test('A successful action on the EditCaseItem page', function () {
    $caseItem = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $caseItem->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditCaseItemRequestFactory::new()->create());

    livewire(CaseItemResource\Pages\EditCaseItem::class, [
        'record' => $caseItem->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'institution_id',
                'status_id',
                'priority_id',
                'type_id',
            ]
        )->toArray()
    );

    $caseItem->refresh();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution_id'))
        ->and($caseItem->status->id)
        ->toEqual($request->get('status_id'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type_id'));
});

test('EditCaseItem requires valid data', function ($data, $errors) {
    $caseItem = ServiceRequest::factory()->create();

    asSuperAdmin();

    $request = collect(EditCaseItemRequestFactory::new($data)->create());

    livewire(CaseItemResource\Pages\EditCaseItem::class, [
        'record' => $caseItem->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequest::class, $caseItem->toArray());

    expect($caseItem->fresh()->institution->id)
        ->toEqual($caseItem->institution->id)
        ->and($caseItem->fresh()->status->id)
        ->toEqual($caseItem->status->id)
        ->and($caseItem->fresh()->priority->id)
        ->toEqual($caseItem->priority->id)
        ->and($caseItem->fresh()->type->id)
        ->toEqual($caseItem->type->id);
})->with(
    [
        'institution_id missing' => [EditCaseItemRequestFactory::new()->state(['institution_id' => null]), ['institution_id' => 'required']],
        'institution_id does not exist' => [
            EditCaseItemRequestFactory::new()->state(['institution_id' => fake()->uuid()]),
            ['institution_id' => 'exists'],
        ],
        'status_id missing' => [EditCaseItemRequestFactory::new()->state(['status_id' => null]), ['status_id' => 'required']],
        'status_id does not exist' => [
            EditCaseItemRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'priority_id missing' => [EditCaseItemRequestFactory::new()->state(['priority_id' => null]), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            EditCaseItemRequestFactory::new()->state(['priority_id' => fake()->uuid()]),
            ['priority_id' => 'exists'],
        ],
        'type_id missing' => [EditCaseItemRequestFactory::new()->state(['type_id' => null]), ['type_id' => 'required']],
        'type_id does not exist' => [
            EditCaseItemRequestFactory::new()->state(['type_id' => fake()->uuid()]),
            ['type_id' => 'exists'],
        ],
        'close_details is not a string' => [EditCaseItemRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [EditCaseItemRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

test('casenumber cannot be edited on EditCaseItem Page', function () {
    $caseItem = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $caseItem->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditCaseItemRequestFactory::new()->create());

    $request->merge(['casenumber' => fake()->randomNumber(9)]);

    livewire(CaseItemResource\Pages\EditCaseItem::class, [
        'record' => $caseItem->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'casenumber',
                'institution_id',
                'status_id',
                'priority_id',
                'type_id',
            ]
        )->toArray()
    );

    $caseItem->refresh();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution_id'))
        ->and($caseItem->status->id)
        ->toEqual($request->get('status_id'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type_id'))
        ->and($caseItem->casenumber)
        ->not()
        ->toEqual($request->get('casenumber'));
});

// Permission Tests

test('EditCaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseItem = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $caseItem,
            ])
        )->assertForbidden();

    $request = collect(EditCaseItemRequestFactory::new()->create());

    $request->merge(['casenumber' => fake()->randomNumber(9)]);

    livewire(CaseItemResource\Pages\EditCaseItem::class, [
        'record' => $caseItem->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('case_item.view-any');
    $user->givePermissionTo('case_item.*.update');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $caseItem,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseItemRequestFactory::new()->create());

    livewire(CaseItemResource\Pages\EditCaseItem::class, [
        'record' => $caseItem->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'institution',
                'status',
                'priority',
                'type',
            ]
        )->toArray()
    );

    $caseItem->refresh();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution_id'))
        ->and($caseItem->status->id)
        ->toEqual($request->get('status_id'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type_id'));
});
