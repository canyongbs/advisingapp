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

use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;
use Assist\ServiceManagement\Tests\RequestFactories\EditServiceRequestPriorityRequestFactory;

test('A successful action on the EditServiceRequestPriority page', function () {
    $serviceRequestPriority = ServiceRequestPriority::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestPriorityResource::getUrl('edit', [
                'record' => $serviceRequestPriority->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestPriorityRequestFactory::new()->create();

    livewire(ServiceRequestPriorityResource\Pages\EditServiceRequestPriority::class, [
        'record' => $serviceRequestPriority->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $serviceRequestPriority->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $serviceRequestPriority->fresh()->name);
});

test('EditServiceRequestPriority requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $serviceRequestPriority = ServiceRequestPriority::factory()->create();

    livewire(ServiceRequestPriorityResource\Pages\EditServiceRequestPriority::class, [
        'record' => $serviceRequestPriority->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $serviceRequestPriority->name,
        ])
        ->fillForm(EditServiceRequestPriorityRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestPriority::class, $serviceRequestPriority->toArray());
})->with(
    [
        'name missing' => [EditServiceRequestPriorityRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditServiceRequestPriorityRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequestPriority is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('edit', [
                'record' => $serviceRequestPriority,
            ])
        )->assertForbidden();

    livewire(ServiceRequestPriorityResource\Pages\EditServiceRequestPriority::class, [
        'record' => $serviceRequestPriority->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request_priority.view-any');
    $user->givePermissionTo('service_request_priority.*.update');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('edit', [
                'record' => $serviceRequestPriority,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestPriorityRequestFactory::new()->create());

    livewire(ServiceRequestPriorityResource\Pages\EditServiceRequestPriority::class, [
        'record' => $serviceRequestPriority->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestPriority->fresh()->name);
});
