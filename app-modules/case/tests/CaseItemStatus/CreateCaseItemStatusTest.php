<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;
use Assist\Case\Models\CaseItemStatus;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Case\Filament\Resources\CaseItemStatusResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemStatusRequestFactory;

test('A successful action on the CreateCaseItemStatus page', function () {
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

// Permission Tests

test('CreateCaseItemStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            CaseItemStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('case_item_status.view-any');
    $user->givePermissionTo('case_item_status.create');

    actingAs($user)
        ->get(
            CaseItemStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseItemStatusRequestFactory::new()->create());

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseItemStatus::all());

    assertDatabaseHas(CaseItemStatus::class, $request->toArray());
});
