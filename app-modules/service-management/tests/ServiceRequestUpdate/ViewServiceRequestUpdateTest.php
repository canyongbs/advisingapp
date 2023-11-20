<?php

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
