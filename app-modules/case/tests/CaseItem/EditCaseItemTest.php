<?php

use Assist\Case\Models\CaseItem;

use function Tests\asSuperAdmin;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseItemResource;
use Assist\Case\Tests\RequestFactories\EditCaseItemRequestFactory;

test('A successful action on the EditCaseItem page', function () {
    $caseItem = CaseItem::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemResource::getUrl('edit', [
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
        CaseItem::class,
        $request->except(
            [
                'institution',
                'state',
                'priority',
                'type',
            ]
        )->toArray()
    );

    $caseItem->refresh();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution'))
        ->and($caseItem->state->id)
        ->toEqual($request->get('state'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type'));
});

test('EditCaseItem requires valid data', function ($data, $errors) {
    $caseItem = CaseItem::factory()->create();

    asSuperAdmin();

    $request = collect(EditCaseItemRequestFactory::new($data)->create());

    livewire(CaseItemResource\Pages\EditCaseItem::class, [
        'record' => $caseItem->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CaseItem::class, $caseItem->toArray());

    expect($caseItem->fresh()->institution->id)
        ->toEqual($caseItem->institution->id)
        ->and($caseItem->fresh()->state->id)
        ->toEqual($caseItem->state->id)
        ->and($caseItem->fresh()->priority->id)
        ->toEqual($caseItem->priority->id)
        ->and($caseItem->fresh()->type->id)
        ->toEqual($caseItem->type->id);
})->with(
    [
        'institution missing' => [EditCaseItemRequestFactory::new()->state(['institution' => null]), ['institution' => 'required']],
        'institution does not exist' => [
            EditCaseItemRequestFactory::new()->state(['institution' => 99]),
            ['institution' => 'exists'],
        ],
        'state missing' => [EditCaseItemRequestFactory::new()->state(['state' => null]), ['state' => 'required']],
        'state does not exist' => [
            EditCaseItemRequestFactory::new()->state(['state' => 99]),
            ['state' => 'exists'],
        ],
        'priority missing' => [EditCaseItemRequestFactory::new()->state(['priority' => null]), ['priority' => 'required']],
        'priority does not exist' => [
            EditCaseItemRequestFactory::new()->state(['priority' => 99]),
            ['priority' => 'exists'],
        ],
        'type missing' => [EditCaseItemRequestFactory::new()->state(['type' => null]), ['type' => 'required']],
        'type does not exist' => [
            EditCaseItemRequestFactory::new()->state(['type' => 99]),
            ['type' => 'exists'],
        ],
        'close_details is not a string' => [EditCaseItemRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [EditCaseItemRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

test('casenumber cannot be edited on EditCaseItem Page', function () {
    $caseItem = CaseItem::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemResource::getUrl('edit', [
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
        CaseItem::class,
        $request->except(
            [
                'casenumber',
                'institution',
                'state',
                'priority',
                'type',
            ]
        )->toArray()
    );

    $caseItem->refresh();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution'))
        ->and($caseItem->state->id)
        ->toEqual($request->get('state'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type'))
        ->and($caseItem->casenumber)
        ->not()
        ->toEqual($request->get('casenumber'));
});
