<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequestStatus;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

test('The correct details are displayed on the ViewServiceRequestStatus page', function () {
    $caseItemStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $caseItemStatus,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $caseItemStatus->id,
                'Name',
                $caseItemStatus->name,
                'Color',
                $caseItemStatus->color,
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

    $user->givePermissionTo('case_item_status.view-any');
    $user->givePermissionTo('case_item_status.*.view');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();
});
