<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateServiceRequestRequestFactory;

test('A successful action on the CreateServiceRequest page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateServiceRequestRequestFactory::new()->create());

    livewire(ServiceRequestResource\Pages\CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
                'type_id',
            ]
        )->toArray()
    );

    $serviceRequest = ServiceRequest::first();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($serviceRequest->type->id)
        ->toEqual($request->get('type_id'));
});

test('CreateServiceRequest requires valid data', function ($data, $errors, $setup = null) {
    if ($setup) {
        $setup();
    }

    asSuperAdmin();

    $request = collect(CreateServiceRequestRequestFactory::new($data)->create());

    livewire(ServiceRequestResource\Pages\CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(ServiceRequest::class, $request->except(['division', 'status', 'priority', 'type'])->toArray());
})->with(
    [
        'division_id missing' => [CreateServiceRequestRequestFactory::new()->without('division_id'), ['division_id' => 'required']],
        'division_id does not exist' => [
            CreateServiceRequestRequestFactory::new()->state(['division_id' => fake()->uuid()]),
            ['division_id' => 'exists'],
        ],
        'status_id missing' => [CreateServiceRequestRequestFactory::new()->without('status_id'), ['status_id' => 'required']],
        'status_id does not exist' => [
            CreateServiceRequestRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'priority_id missing' => [CreateServiceRequestRequestFactory::new()->without('priority_id'), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            CreateServiceRequestRequestFactory::new()->state(['priority_id' => fake()->uuid()]),
            ['priority_id' => 'exists'],
        ],
        'type_id missing' => [CreateServiceRequestRequestFactory::new()->without('type_id'), ['type_id' => 'required']],
        'type_id does not exist' => [
            CreateServiceRequestRequestFactory::new()->state(['type_id' => fake()->uuid()]),
            ['type_id' => 'exists'],
        ],
        'close_details is not a string' => [CreateServiceRequestRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [CreateServiceRequestRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('CreateServiceRequest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertForbidden();

    livewire(ServiceRequestResource\Pages\CreateServiceRequest::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestRequestFactory::new()->create());

    livewire(ServiceRequestResource\Pages\CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
                'type_id',
            ]
        )->toArray()
    );

    $serviceRequest = ServiceRequest::first();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($serviceRequest->type->id)
        ->toEqual($request->get('type_id'));
});
