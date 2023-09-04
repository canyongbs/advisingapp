<?php

use App\Models\User;
use Illuminate\Validation\Rules\Enum;
use Assist\Case\Models\ServiceRequestUpdate;
use Assist\Case\Filament\Resources\CaseUpdateResource;
use Assist\Case\Tests\RequestFactories\EditCaseUpdateRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;

test('A successful action on the EditCaseUpdate page', function () {
    $caseUpdate = ServiceRequestUpdate::factory()->create();

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

    assertDatabaseHas(ServiceRequestUpdate::class, $request->except('case_id')->toArray());

    expect(ServiceRequestUpdate::first()->case->id)
        ->toEqual($request->get('case_id'));
});

test('EditCaseUpdate requires valid data', function ($data, $errors) {
    $caseUpdate = ServiceRequestUpdate::factory()->create();

    asSuperAdmin();

    livewire(CaseUpdateResource\Pages\EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm(EditCaseUpdateRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestUpdate::class, $caseUpdate->toArray());

    expect(ServiceRequestUpdate::first()->case->id)
        ->toEqual($caseUpdate->case->id);
})->with(
    [
        'case missing' => [EditCaseUpdateRequestFactory::new()->state(['case_id' => null]), ['case_id' => 'required']],
        'case not existing case id' => [EditCaseUpdateRequestFactory::new()->state(['case_id' => fake()->uuid()]), ['case_id' => 'exists']],
        'update missing' => [EditCaseUpdateRequestFactory::new()->state(['update' => null]), ['update' => 'required']],
        'update is not a string' => [EditCaseUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'direction missing' => [EditCaseUpdateRequestFactory::new()->state(['direction' => null]), ['direction' => 'required']],
        'direction not a valid enum' => [EditCaseUpdateRequestFactory::new()->state(['direction' => 'invalid']), ['direction' => Enum::class]],
        'internal not a boolean' => [EditCaseUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);

// Permission Tests

test('EditCaseUpdate is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseUpdate = ServiceRequestUpdate::factory()->create();

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate,
            ])
        )->assertForbidden();

    livewire(CaseUpdateResource\Pages\EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('case_update.view-any');
    $user->givePermissionTo('case_update.*.update');

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseUpdateRequestFactory::new()->create());

    livewire(CaseUpdateResource\Pages\EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestUpdate::class, $request->except('case_id')->toArray());

    expect(ServiceRequestUpdate::first()->case->id)
        ->toEqual($request->get('case_id'));
});
