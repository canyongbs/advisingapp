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
                'respondent.display_name',
                $serviceRequest->respondent->full_name,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'respondent.sisid',
                $serviceRequest->respondent->sisid,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'respondent.otherid',
                $serviceRequest->respondent->otherid,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'institution.name',
                $serviceRequest->institution->name,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'assignedTo.name',
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
