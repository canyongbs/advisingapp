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

use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateServiceRequestTypeRequestFactory;

test('A successful action on the CreateServiceRequestType page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('create')
        )
        ->assertSuccessful();

    $editRequest = CreateServiceRequestTypeRequestFactory::new()->create();

    livewire(ServiceRequestTypeResource\Pages\CreateServiceRequestType::class)
        ->fillForm($editRequest)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestType::all());

    assertDatabaseHas(ServiceRequestType::class, $editRequest);
});

test('CreateServiceRequestType requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ServiceRequestTypeResource\Pages\CreateServiceRequestType::class)
        ->fillForm(CreateServiceRequestTypeRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestType::all());
})->with(
    [
        'name missing' => [CreateServiceRequestTypeRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateServiceRequestTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('CreateServiceRequestType is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('create')
        )->assertForbidden();

    livewire(ServiceRequestTypeResource\Pages\CreateServiceRequestType::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');
    $user->givePermissionTo('service_request_type.create');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestTypeRequestFactory::new()->create());

    livewire(ServiceRequestTypeResource\Pages\CreateServiceRequestType::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestType::all());

    assertDatabaseHas(ServiceRequestType::class, $request->toArray());
});
