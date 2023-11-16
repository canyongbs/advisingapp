<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

test('The correct details are displayed on the ListServiceRequests page', function () {
    $serviceRequests = ServiceRequest::factory()
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ServiceRequestResource\Pages\ListServiceRequests::class);

    $component->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequests)
        ->assertCountTableRecords(10);

    $serviceRequests->each(
        fn (ServiceRequest $serviceRequest) => $component
            ->assertTableColumnStateSet(
                'service_request_number',
                $serviceRequest->service_request_number,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'respondent_name',
                $serviceRequest->respondent->full_name,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'respondent_id',
                $serviceRequest->respondent->sisid,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'respondent_otherid',
                $serviceRequest->respondent->otherid,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'division_name',
                $serviceRequest->division->name,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'assigned_to_name',
                $serviceRequest->assignedTo->name,
                $serviceRequest
            )
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequests is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertSuccessful();
});
