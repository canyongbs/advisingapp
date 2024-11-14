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
use Illuminate\Support\Str;

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\CaseManagement\Models\ServiceRequest;
use AdvisingApp\CaseManagement\Models\ServiceRequestUpdate;
use AdvisingApp\CaseManagement\Filament\Resources\ServiceRequestUpdateResource;

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
})->skip();

test('ListServiceRequestUpdates is gated with proper access control for superAdmin', function () {
    asSuperAdmin()->get(
        ServiceRequestUpdateResource::getUrl('index')
    )->assertStatus(403);
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequestUpdates is gated with proper access control', function () {
    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request_update.view-any');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('index')
        )->assertStatus(403);
});

test('ListServiceRequestUpdates is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    $user->givePermissionTo('service_request_update.view-any');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl()
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl()
        )->assertStatus(403);
});
