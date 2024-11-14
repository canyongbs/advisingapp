<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Models\ServiceRequest;
use AdvisingApp\CaseManagement\Filament\Resources\ServiceRequestResource;
use AdvisingApp\CaseManagement\Tests\RequestFactories\EditServiceRequestRequestFactory;
use AdvisingApp\CaseManagement\Filament\Resources\ServiceRequestResource\Pages\EditServiceRequest;

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

    livewire(EditServiceRequest::class, [
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
            ]
        )->toArray()
    );

    $serviceRequest->refresh();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('EditServiceRequest requires valid data', function ($data, $errors) {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin();

    $request = collect(EditServiceRequestRequestFactory::new($data)->create());

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequest::class, $serviceRequest->withoutRelations()->toArray());

    expect($serviceRequest->fresh()->division->id)
        ->toEqual($serviceRequest->division->id)
        ->and($serviceRequest->fresh()->status->id)
        ->toEqual($serviceRequest->status->id)
        ->and($serviceRequest->fresh()->priority->id)
        ->toEqual($serviceRequest->priority->id);
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
        'close_details is not a string' => [EditServiceRequestRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [EditServiceRequestRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequest is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $serviceRequest = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    livewire(EditServiceRequest::class, [
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

    livewire(EditServiceRequest::class, [
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
                'priority',
            ]
        )->toArray()
    );

    $serviceRequest->refresh();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('EditServiceRequest is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    $serviceRequest = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestRequestFactory::new()->create());

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceRequest->fresh()->only($request->except('division_id')->keys()->toArray()))
        ->toEqual($request->except('division_id')->toArray())
        ->and($serviceRequest->fresh()->division->id)->toEqual($request['division_id']);
});
