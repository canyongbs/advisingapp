<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;

test('The correct details are displayed on the ViewServiceRequestPriority page', function () {
    $serviceRequestPriority = ServiceRequestPriority::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestPriorityResource::getUrl('view', [
                'record' => $serviceRequestPriority,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $serviceRequestPriority->name,
                'Order',
                $serviceRequestPriority->order,
            ]
        );
});

// Permission Tests

test('ViewServiceRequestPriority is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectSource = ServiceRequestPriority::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request_priority.view-any');
    $user->givePermissionTo('service_request_priority.*.view');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();
});
