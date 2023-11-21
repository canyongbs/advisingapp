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
use Illuminate\Support\Str;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;

test('The correct details are displayed on the ListServiceRequestUpdates page', function () {
    $serviceRequestUpdates = ServiceRequestUpdate::factory()
        ->for(ServiceRequest::factory(), 'serviceRequest')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ServiceRequestUpdateResource\Pages\ListServiceRequestUpdates::class);

    $component->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequestUpdates)
        ->assertCountTableRecords(10);

    $serviceRequestUpdates->each(
        fn (ServiceRequestUpdate $serviceRequestUpdate) => $component
            ->assertTableColumnStateSet(
                'id',
                $serviceRequestUpdate->id,
                $serviceRequestUpdate
            )
            ->assertTableColumnStateSet(
                'serviceRequest.respondent.full',
                $serviceRequestUpdate->serviceRequest->respondent->full,
                $serviceRequestUpdate
            )
            ->assertTableColumnStateSet(
                'serviceRequest.respondent.sisid',
                $serviceRequestUpdate->serviceRequest->respondent->sisid,
                $serviceRequestUpdate
            )
            ->assertTableColumnStateSet(
                'serviceRequest.respondent.otherid',
                $serviceRequestUpdate->serviceRequest->respondent->otherid,
                $serviceRequestUpdate
            )
            ->assertTableColumnStateSet(
                'serviceRequest.service_request_number',
                $serviceRequestUpdate->serviceRequest->service_request_number,
                $serviceRequestUpdate
            )
            ->assertTableColumnStateSet(
                'internal',
                $serviceRequestUpdate->internal,
                $serviceRequestUpdate
            )
            ->assertTableColumnFormattedStateSet(
                'direction',
                Str::ucfirst($serviceRequestUpdate->direction->value),
                $serviceRequestUpdate
            )
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequestUpdates is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request_update.view-any');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('index')
        )->assertSuccessful();
});
