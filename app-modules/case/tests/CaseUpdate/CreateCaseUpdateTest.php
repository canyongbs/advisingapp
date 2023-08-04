<?php

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseUpdate;

use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseUpdateResource;
use Assist\Case\Tests\RequestFactories\CreateCaseUpdateRequestFactory;

test('A successful action on the CreateCaseUpdate page', function () {
    asSuperAdmin()
        ->get(
            CaseUpdateResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateCaseUpdateRequestFactory::new()->create());

    livewire(CaseUpdateResource\Pages\CreateCaseUpdate::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseUpdate::all());

    assertDatabaseHas(CaseUpdate::class, $request->except('case')->toArray());

    expect(CaseUpdate::first()->case->id)
        ->toEqual($request->get('case'));
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
