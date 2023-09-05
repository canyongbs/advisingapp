<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

test('The correct details are displayed on the ViewServiceRequestPriority page', function () {
    $caseItemPriority = ServiceRequestPriority::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestPriorityResource::getUrl('view', [
                'record' => $caseItemPriority,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $caseItemPriority->name,
                'Order',
                $caseItemPriority->order,
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

    $user->givePermissionTo('case_item_priority.view-any');
    $user->givePermissionTo('case_item_priority.*.view');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();
});
