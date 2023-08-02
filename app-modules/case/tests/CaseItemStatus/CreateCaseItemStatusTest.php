<?php

use function Tests\asSuperAdmin;
use function Pest\Laravel\artisan;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;
use Assist\Case\Models\CaseItemStatus;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseItemStatusResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemStatusRequestFactory;

test('A successful action on the CreateCaseItemStatus page', function () {
    artisan('roles-and-permissions:sync');

    asSuperAdmin()
        ->get(
            CaseItemStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateCaseItemStatusRequestFactory::new()->create();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseItemStatus::all());

    assertDatabaseHas(CaseItemStatus::class, $request);
});

test('CreateCaseItemStatus requires valid data', function ($data, $errors) {
    // TODO: Bring back once we figure out how to speed this up
    //artisan('roles-and-permissions:sync');

    asSuperAdmin();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm(CreateCaseItemStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(CaseItemStatus::all());
})->with(
    [
        'name missing' => [CreateCaseItemStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateCaseItemStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateCaseItemStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateCaseItemStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);
