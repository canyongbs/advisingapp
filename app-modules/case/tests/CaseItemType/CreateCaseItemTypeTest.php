<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Case\Models\CaseItemType;

use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseItemTypeResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemTypeRequestFactory;

test('A successful action on the CreateCaseItemType page', function () {
    asSuperAdmin()
        ->get(
            CaseItemTypeResource::getUrl('create')
        )
        ->assertSuccessful();

    $editRequest = CreateCaseItemTypeRequestFactory::new()->create();

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->fillForm($editRequest)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseItemType::all());

    assertDatabaseHas(CaseItemType::class, $editRequest);
});

test('CreateCaseItemType requires valid data', function ($data, $errors) {
    asSuperAdmin();

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

// Permission Tests

test('CreateCaseItemType is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            CaseItemTypeResource::getUrl('create')
        )->assertForbidden();

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->assertForbidden();

    $user->givePermissionTo('case_item_type.view-any');
    $user->givePermissionTo('case_item_type.create');

    actingAs($user)
        ->get(
            CaseItemTypeResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseItemTypeRequestFactory::new()->create());

    livewire(CaseItemTypeResource\Pages\CreateCaseItemType::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseItemType::all());

    assertDatabaseHas(CaseItemType::class, $request->toArray());
});
