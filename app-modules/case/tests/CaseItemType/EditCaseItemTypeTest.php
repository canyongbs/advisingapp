<?php

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseItemType;

use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\Case\Filament\Resources\CaseItemTypeResource;
use Assist\Case\Tests\RequestFactories\EditCaseItemTypeRequestFactory;

test('A successful action on the EditCaseItemType page', function () {
    $caseItemType = CaseItemType::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemTypeResource::getUrl('edit', [
                'record' => $caseItemType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCaseItemTypeRequestFactory::new()->create();

    livewire(CaseItemTypeResource\Pages\EditCaseItemType::class, [
        'record' => $caseItemType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemType->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $caseItemType->fresh()->name);
});

test('EditCaseItemType requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $caseItemType = CaseItemType::factory()->create();

    livewire(CaseItemTypeResource\Pages\EditCaseItemType::class, [
        'record' => $caseItemType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemType->name,
        ])
        ->fillForm(EditCaseItemTypeRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CaseItemType::class, $caseItemType->toArray());
})->with(
    [
        'name missing' => [EditCaseItemTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditCaseItemTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);
