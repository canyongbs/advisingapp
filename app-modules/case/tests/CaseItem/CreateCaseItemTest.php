<?php

use Assist\Case\Models\CaseItem;

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseUpdate;

use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseItemResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemRequestFactory;
use Assist\Case\Tests\RequestFactories\CreateCaseUpdateRequestFactory;

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

test('CreateCaseUpdate requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CaseUpdateResource\Pages\CreateCaseUpdate::class)
        ->fillForm(CreateCaseUpdateRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(CaseUpdate::all());
})->with(
    [
        'case missing' => [CreateCaseUpdateRequestFactory::new()->without('case'), ['case' => 'required']],
        'case not existing case id' => [CreateCaseUpdateRequestFactory::new()->state(['case' => 99]), ['case' => 'exists']],
        'update missing' => [CreateCaseUpdateRequestFactory::new()->without('update'), ['update' => 'required']],
        'update is not a string' => [CreateCaseUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'direction missing' => [CreateCaseUpdateRequestFactory::new()->without('direction'), ['direction' => 'required']],
        'direction not a valid enum' => [CreateCaseUpdateRequestFactory::new()->state(['direction' => 'invalid']), ['direction' => Enum::class]],
        'internal not a boolean' => [CreateCaseUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);
