<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

test('The correct details are displayed on the ViewServiceRequest page', function () {
    $caseItem = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $caseItem,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $caseItem->id,
                'Case Number',
                $caseItem->casenumber,
                'Institution',
                $caseItem->institution->name,
                'Status',
                $caseItem->status->name,
                'Priority',
                $caseItem->priority->name,
                'Type',
                $caseItem->type->name,
                'Close Details/Description',
                $caseItem->close_details,
                'Internal Case Details',
                $caseItem->res_details,
            ]
        );
});

// Permission Tests

test('ViewServiceRequest is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseItem = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $caseItem,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_item.view-any');
    $user->givePermissionTo('case_item.*.view');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $caseItem,
            ])
        )->assertSuccessful();
});
