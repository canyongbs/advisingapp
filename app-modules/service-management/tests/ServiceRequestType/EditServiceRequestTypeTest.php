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
use function PHPUnit\Framework\assertEquals;

use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestTypeRequestFactory;

test('A successful action on the EditServiceRequestType page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestTypeRequestFactory::new()->create();

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $serviceRequestType->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $serviceRequestType->fresh()->name);
});

test('EditServiceRequestType requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $serviceRequestType->name,
        ])
        ->fillForm(EditServiceRequestTypeRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestType::class, $serviceRequestType->toArray());
})->with(
    [
        'name missing' => [EditServiceRequestTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditServiceRequestTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequestType is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $serviceRequestType,
            ])
        )->assertForbidden();

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');
    $user->givePermissionTo('service_request_type.*.update');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('edit', [
                'record' => $serviceRequestType,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestTypeRequestFactory::new()->create());

    livewire(ServiceRequestTypeResource\Pages\EditServiceRequestType::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestType->fresh()->name);
});
