<?php

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
