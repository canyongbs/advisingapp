<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

test('The correct details are displayed on the ViewServiceRequest page', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $serviceRequest->id,
                'Service Request Number',
                $serviceRequest->service_request_number,
                'Division',
                $serviceRequest->division->name,
                'Status',
                $serviceRequest->status->name,
                'Priority',
                $serviceRequest->priority->name,
                'Type',
                $serviceRequest->type->name,
                'Close Details/Description',
                $serviceRequest->close_details,
                'Internal Service Request Details',
                $serviceRequest->res_details,
            ]
        );
});

// Permission Tests

test('ViewServiceRequest is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();
});
