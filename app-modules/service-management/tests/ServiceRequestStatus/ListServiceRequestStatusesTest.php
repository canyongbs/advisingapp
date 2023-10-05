<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;

test('The correct details are displayed on the ListServiceRequestStatuses page', function () {
    $serviceRequestStatuses = ServiceRequestStatus::factory()
        ->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'serviceRequests')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ServiceRequestStatusResource\Pages\ListServiceRequestStatuses::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequestStatuses)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('service_requests_count');

    $serviceRequestStatuses->each(
        fn (ServiceRequestStatus $serviceRequestType) => $component
            ->assertTableColumnStateSet(
                'id',
                $serviceRequestType->id,
                $serviceRequestType
            )
            ->assertTableColumnStateSet(
                'name',
                $serviceRequestType->name,
                $serviceRequestType
            )
            ->assertTableColumnStateSet(
                'color',
                $serviceRequestType->color,
                $serviceRequestType
            )
        // Currently setting not test for service_request_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequestStatuses is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request_status.view-any');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('index')
        )->assertSuccessful();
});
