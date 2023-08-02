<?php

use function Tests\asSuperAdmin;
use function Pest\Laravel\artisan;
use function Pest\Livewire\livewire;

use Assist\Case\Models\CaseItemPriority;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\Case\Filament\Resources\CaseItemPriorityResource;
use Assist\Case\Tests\RequestFactories\EditCaseItemPriorityRequestFactory;

test('A successful action on the EditCaseItemPriority page', function () {
    artisan('roles-and-permissions:sync');

    $caseItemPriority = CaseItemPriority::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemPriorityResource::getUrl('edit', [
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

test('EditCaseItemPriority requires valid data', function ($data, $errors) {
    artisan('roles-and-permissions:sync');

    asSuperAdmin();

    $caseItemPriority = CaseItemPriority::factory()->create();

    livewire(CaseItemPriorityResource\Pages\EditCaseItemPriority::class, [
        'record' => $caseItemPriority->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseItemPriority->name,
        ])
        ->fillForm(EditCaseItemPriorityRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CaseItemPriority::class, $caseItemPriority->toArray());
})->with(
    [
        'name missing' => [EditCaseItemPriorityRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditCaseItemPriorityRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);
