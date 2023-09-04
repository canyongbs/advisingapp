<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

test('The correct details are displayed on the ViewCaseItemPriority page', function () {
    $caseItemPriority = ServiceRequestPriority::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemPriorityResource::getUrl('view', [
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

test('ViewCaseItemPriority is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectSource = ServiceRequestPriority::factory()->create();

    actingAs($user)
        ->get(
            CaseItemPriorityResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_item_priority.view-any');
    $user->givePermissionTo('case_item_priority.*.view');

    actingAs($user)
        ->get(
            CaseItemPriorityResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();
});
