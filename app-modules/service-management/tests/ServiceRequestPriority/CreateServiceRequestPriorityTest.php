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
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateServiceRequestPriorityRequestFactory;

test('A successful action on the CreateServiceRequestPriority page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestPriorityResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateServiceRequestPriorityRequestFactory::new()->create();

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestPriority::all());

    assertDatabaseHas(ServiceRequestPriority::class, $request);
});

test('CreateServiceRequestPriority requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->fillForm(CreateServiceRequestPriorityRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestPriority::all());
})->with(
    [
        'name missing' => [CreateServiceRequestPriorityRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateServiceRequestPriorityRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'order missing' => [CreateServiceRequestPriorityRequestFactory::new()->without('order'), ['order' => 'required']],
        'order not a number' => [CreateServiceRequestPriorityRequestFactory::new()->state(['order' => 'a']), ['order' => 'numeric']],
    ]
);

// Permission Tests

test('CreateServiceRequestPriority is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('create')
        )->assertForbidden();

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request_priority.view-any');
    $user->givePermissionTo('service_request_priority.create');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestPriorityRequestFactory::new()->create());

    livewire(ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestPriority::all());

    assertDatabaseHas(ServiceRequestPriority::class, $request->toArray());
});
