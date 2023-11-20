<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;

test('The correct details are displayed on the ViewServiceRequestStatus page', function () {
    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $serviceRequestStatus,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $serviceRequestStatus->name,
                'Color',
                $serviceRequestStatus->color,
            ]
        );
});

// Permission Tests

test('ViewServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectSource = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request_status.view-any');
    $user->givePermissionTo('service_request_status.*.view');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();
});
