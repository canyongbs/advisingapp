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

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;

test('The correct details are displayed on the ListServiceRequestPriorities page', function () {
    $serviceRequestPriorities = ServiceRequestPriority::factory()
        ->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'serviceRequests')
        ->count(3)
        ->sequence(
            ['name' => 'High', 'order' => 1],
            ['name' => 'Medium', 'order' => 2],
            ['name' => 'Low', 'order' => 3],
        )
        ->create();

    asSuperAdmin();

    $component = livewire(ServiceRequestPriorityResource\Pages\ListServiceRequestPriorities::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequestPriorities)
        ->assertCountTableRecords(3)
        ->assertTableColumnExists('service_requests_count');

    $serviceRequestPriorities->each(
        fn (ServiceRequestPriority $serviceRequestPriority) => $component
            ->assertTableColumnStateSet(
                'name',
                $serviceRequestPriority->name,
                $serviceRequestPriority
            )
            ->assertTableColumnStateSet(
                'order',
                $serviceRequestPriority->order,
                $serviceRequestPriority
            )
        // Currently setting not test for service_request_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequestPriorities is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request_priority.view-any');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('index')
        )->assertSuccessful();
});
