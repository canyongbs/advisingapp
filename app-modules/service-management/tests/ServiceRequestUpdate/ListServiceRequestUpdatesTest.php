<?php

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
