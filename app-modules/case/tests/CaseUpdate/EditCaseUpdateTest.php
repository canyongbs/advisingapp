<?php

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseUpdate;

use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseUpdateResource;
use Assist\Case\Tests\RequestFactories\EditCaseUpdateRequestFactory;

test('A successful action on the EditCaseUpdate page', function () {
    $caseUpdate = CaseUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditCaseUpdateRequestFactory::new()->create());

    livewire(CaseUpdateResource\Pages\EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(CaseUpdate::class, $request->except('case')->toArray());

    expect(CaseUpdate::first()->case->id)
        ->toEqual($request->get('case'));
});

test('EditCaseUpdate requires valid data', function ($data, $errors) {
    $caseUpdate = CaseUpdate::factory()->create();

    asSuperAdmin();

    livewire(CaseUpdateResource\Pages\EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm(EditCaseUpdateRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CaseUpdate::class, $caseUpdate->toArray());

    expect(CaseUpdate::first()->case->id)
        ->toEqual($caseUpdate->case->id);
})->with(
    [
        'case missing' => [EditCaseUpdateRequestFactory::new()->state(['case' => null]), ['case' => 'required']],
        'case not existing case id' => [EditCaseUpdateRequestFactory::new()->state(['case' => 99]), ['case' => 'exists']],
        'update missing' => [EditCaseUpdateRequestFactory::new()->state(['update' => null]), ['update' => 'required']],
        'update is not a string' => [EditCaseUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'direction missing' => [EditCaseUpdateRequestFactory::new()->state(['direction' => null]), ['direction' => 'required']],
        'direction not a valid enum' => [EditCaseUpdateRequestFactory::new()->state(['direction' => 'invalid']), ['direction' => Enum::class]],
        'internal not a boolean' => [EditCaseUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);
