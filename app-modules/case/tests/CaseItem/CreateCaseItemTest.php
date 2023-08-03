<?php

use Assist\Case\Models\CaseItem;

use function Tests\asSuperAdmin;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Assist\Case\Filament\Resources\CaseItemResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemRequestFactory;

test('A successful action on the CreateCaseUpdate page', function () {
    asSuperAdmin()
        ->get(
            CaseItemResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateCaseItemRequestFactory::new()->create());

    livewire(CaseItemResource\Pages\CreateCaseItem::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseItem::all());

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

    $caseItem = CaseItem::first();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution'))
        ->and($caseItem->state->id)
        ->toEqual($request->get('state'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type'));
});

test('CreateCaseItem requires valid data', function ($data, $errors, $setup = null) {
    if ($setup) {
        $setup();
    }

    asSuperAdmin();

    $request = collect(CreateCaseItemRequestFactory::new($data)->create());

    livewire(CaseItemResource\Pages\CreateCaseItem::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(CaseItem::class, $request->except(['institution', 'state', 'priority', 'type'])->toArray());
})->with(
    [
        'casenumber missing' => [CreateCaseItemRequestFactory::new()->without('casenumber'), ['casenumber' => 'required']],
        'casenumber should be unique' => [
            CreateCaseItemRequestFactory::new()->state(['casenumber' => 99]),
            ['casenumber' => 'unique'],
            function () {
                CaseItem::factory()->create(['casenumber' => 99]);
            },
        ],
        'institution missing' => [CreateCaseItemRequestFactory::new()->without('institution'), ['institution' => 'required']],
        'institution does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['institution' => 99]),
            ['institution' => 'exists'],
        ],
        'state missing' => [CreateCaseItemRequestFactory::new()->without('state'), ['state' => 'required']],
        'state does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['state' => 99]),
            ['state' => 'exists'],
        ],
        'priority missing' => [CreateCaseItemRequestFactory::new()->without('priority'), ['priority' => 'required']],
        'priority does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['priority' => 99]),
            ['priority' => 'exists'],
        ],
        'type missing' => [CreateCaseItemRequestFactory::new()->without('type'), ['type' => 'required']],
        'type does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['type' => 99]),
            ['type' => 'exists'],
        ],
        'close_details is not a string' => [CreateCaseItemRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [CreateCaseItemRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);
