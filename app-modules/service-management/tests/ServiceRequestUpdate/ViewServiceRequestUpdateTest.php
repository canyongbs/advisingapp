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

use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;

test('The correct details are displayed on the ViewServiceRequestUpdate page', function () {
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Service Request',
                $serviceRequestUpdate->serviceRequest->service_request_number,
                'Internal',
                // TODO: Figure out how to check whether this internal value the check or the X icon
                'Direction',
                $serviceRequestUpdate->direction->name,
                'Update',
                $serviceRequestUpdate->update,
            ]
        );
});

// Permission Tests

test('ViewServiceRequestUpdate is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.view');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertSuccessful();
});
