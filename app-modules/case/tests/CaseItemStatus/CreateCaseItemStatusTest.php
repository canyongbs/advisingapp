<?php

use App\Models\User;
use Illuminate\Validation\Rules\Enum;
use Assist\Case\Models\ServiceRequestStatus;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemStatusRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

test('A successful action on the CreateServiceRequestStatus page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateCaseItemStatusRequestFactory::new()->create();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request);
});

test('CreateServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm(CreateCaseItemStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestStatus::all());
})->with(
    [
        'name missing' => [CreateCaseItemStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateCaseItemStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateCaseItemStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateCaseItemStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('CreateServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('case_item_status.view-any');
    $user->givePermissionTo('case_item_status.create');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseItemStatusRequestFactory::new()->create());

    livewire(CaseItemStatusResource\Pages\CreateCaseItemStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request->toArray());
});
