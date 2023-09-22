<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;

test('The correct details are displayed on the ListServiceRequestType page', function () {
    $serviceRequestTypes = ServiceRequestType::factory()
        ->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'serviceRequests')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ServiceRequestTypeResource\Pages\ListServiceRequestTypes::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequestTypes)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('service_request_count');

    $serviceRequestTypes->each(
        fn (ServiceRequestType $serviceRequestType) => $component
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
        // Currently setting not test for service_request_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequestTypes is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('index')
        )->assertSuccessful();
});
