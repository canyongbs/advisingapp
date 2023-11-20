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
use function Pest\Laravel\assertDatabaseHas;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestRequestFactory;

test('A successful action on the EditServiceRequest page', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditServiceRequestRequestFactory::new()->create());

    livewire(ServiceRequestResource\Pages\EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

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

    $serviceRequest->refresh();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($serviceRequest->type->id)
        ->toEqual($request->get('type_id'));
});

test('EditServiceRequest requires valid data', function ($data, $errors) {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin();

    $request = collect(EditServiceRequestRequestFactory::new($data)->create());

    livewire(ServiceRequestResource\Pages\EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequest::class, $serviceRequest->toArray());

    expect($serviceRequest->fresh()->division->id)
        ->toEqual($serviceRequest->division->id)
        ->and($serviceRequest->fresh()->status->id)
        ->toEqual($serviceRequest->status->id)
        ->and($serviceRequest->fresh()->priority->id)
        ->toEqual($serviceRequest->priority->id)
        ->and($serviceRequest->fresh()->type->id)
        ->toEqual($serviceRequest->type->id);
})->with(
    [
        'division_id missing' => [EditServiceRequestRequestFactory::new()->state(['division_id' => null]), ['division_id' => 'required']],
        'division_id does not exist' => [
            EditServiceRequestRequestFactory::new()->state(['division_id' => fake()->uuid()]),
            ['division_id' => 'exists'],
        ],
        'status_id missing' => [EditServiceRequestRequestFactory::new()->state(['status_id' => null]), ['status_id' => 'required']],
        'status_id does not exist' => [
            EditServiceRequestRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'priority_id missing' => [EditServiceRequestRequestFactory::new()->state(['priority_id' => null]), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            EditServiceRequestRequestFactory::new()->state(['priority_id' => fake()->uuid()]),
            ['priority_id' => 'exists'],
        ],
        'type_id missing' => [EditServiceRequestRequestFactory::new()->state(['type_id' => null]), ['type_id' => 'required']],
        'type_id does not exist' => [
            EditServiceRequestRequestFactory::new()->state(['type_id' => fake()->uuid()]),
            ['type_id' => 'exists'],
        ],
        'close_details is not a string' => [EditServiceRequestRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [EditServiceRequestRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequest is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    livewire(ServiceRequestResource\Pages\EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestRequestFactory::new()->create());

    livewire(ServiceRequestResource\Pages\EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status',
                'priority',
                'type',
            ]
        )->toArray()
    );

    $serviceRequest->refresh();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'))
        ->and($serviceRequest->type->id)
        ->toEqual($request->get('type_id'));
});
