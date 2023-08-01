<?php

use function Tests\asSuperAdmin;
use function Pest\Laravel\artisan;

use Assist\Case\Models\CaseItemType;

use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseItemTypeResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemTypeRequestFactory;

test('A successful action on the CreateCaseItemType page', function () {
    artisan('roles-and-permissions:sync');

    asSuperAdmin()
        ->get(
            CaseItemTypeResource::getUrl('create')
        )
        ->assertSuccessful();

    $data = [
        'name' => fake()->name(),
    ];

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->fillForm($data)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseItemType::all());

    assertDatabaseHas(CaseItemType::class, $data);
});

test('CreateCaseItemType required valid data', function ($data, $errors) {
    artisan('roles-and-permissions:sync');

    asSuperAdmin();

    ray(CreateCaseItemTypeRequestFactory::new($data)->create());

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->fillForm(CreateCaseItemTypeRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(CaseItemType::all());
})->with(
    [
        'name missing' => [CreateCaseItemTypeRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateCaseItemTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);
