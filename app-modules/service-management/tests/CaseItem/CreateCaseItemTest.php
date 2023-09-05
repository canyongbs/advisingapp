<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateCaseItemRequestFactory;

test('A successful action on the CreateServiceRequestUpdate page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateCaseItemRequestFactory::new()->create());

    livewire(CaseItemResource\Pages\CreateCaseItem::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'institution_id',
                'status_id',
                'priority_id',
                'type_id',
            ]
        )->toArray()
    );

    $caseItem = ServiceRequest::first();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution_id'))
        ->and($caseItem->status->id)
        ->toEqual($request->get('status_id'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type_id'));
});

test('CreateServiceRequest requires valid data', function ($data, $errors, $setup = null) {
    if ($setup) {
        $setup();
    }

    asSuperAdmin();

    $request = collect(CreateCaseItemRequestFactory::new($data)->create());

    livewire(CaseItemResource\Pages\CreateCaseItem::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(ServiceRequest::class, $request->except(['institution', 'status', 'priority', 'type'])->toArray());
})->with(
    [
        'casenumber missing' => [CreateCaseItemRequestFactory::new()->without('casenumber'), ['casenumber' => 'required']],
        'casenumber should be unique' => [
            CreateCaseItemRequestFactory::new()->state(['casenumber' => 99]),
            ['casenumber' => 'unique'],
            function () {
                ServiceRequest::factory()->create(['casenumber' => 99]);
            },
        ],
        'institution_id missing' => [CreateCaseItemRequestFactory::new()->without('institution_id'), ['institution_id' => 'required']],
        'institution_id does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['institution_id' => fake()->uuid()]),
            ['institution_id' => 'exists'],
        ],
        'status_id missing' => [CreateCaseItemRequestFactory::new()->without('status_id'), ['status_id' => 'required']],
        'status_id does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'priority_id missing' => [CreateCaseItemRequestFactory::new()->without('priority_id'), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['priority_id' => fake()->uuid()]),
            ['priority_id' => 'exists'],
        ],
        'type_id missing' => [CreateCaseItemRequestFactory::new()->without('type_id'), ['type_id' => 'required']],
        'type_id does not exist' => [
            CreateCaseItemRequestFactory::new()->state(['type_id' => fake()->uuid()]),
            ['type_id' => 'exists'],
        ],
        'close_details is not a string' => [CreateCaseItemRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [CreateCaseItemRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('CreateServiceRequest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertForbidden();

    livewire(CaseItemResource\Pages\CreateCaseItem::class)
        ->assertForbidden();

    $user->givePermissionTo('case_item.view-any');
    $user->givePermissionTo('case_item.create');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseItemRequestFactory::new()->create());

    livewire(CaseItemResource\Pages\CreateCaseItem::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'institution_id',
                'status_id',
                'priority_id',
                'type_id',
            ]
        )->toArray()
    );

    $caseItem = ServiceRequest::first();

    expect($caseItem->institution->id)
        ->toEqual($request->get('institution_id'))
        ->and($caseItem->status->id)
        ->toEqual($request->get('status_id'))
        ->and($caseItem->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($caseItem->type->id)
        ->toEqual($request->get('type_id'));
});
