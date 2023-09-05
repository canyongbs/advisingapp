<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;

test('The correct details are displayed on the ViewServiceRequestType page', function () {
    $caseItemType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('view', [
                'record' => $caseItemType,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $caseItemType->id,
                'Name',
                $caseItemType->name,
            ]
        );
});

// Permission Tests

test('ViewServiceRequestType is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseItemType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('view', [
                'record' => $caseItemType,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_item_type.view-any');
    $user->givePermissionTo('case_item_type.*.view');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('view', [
                'record' => $caseItemType,
            ])
        )->assertSuccessful();
});
