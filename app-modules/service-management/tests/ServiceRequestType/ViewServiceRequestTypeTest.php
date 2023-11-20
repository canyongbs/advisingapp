<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;

test('The correct details are displayed on the ViewServiceRequestType page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('view', [
                'record' => $serviceRequestType,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $serviceRequestType->name,
            ]
        );
});

// Permission Tests

test('ViewServiceRequestType is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('view', [
                'record' => $serviceRequestType,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');
    $user->givePermissionTo('service_request_type.*.view');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('view', [
                'record' => $serviceRequestType,
            ])
        )->assertSuccessful();
});
