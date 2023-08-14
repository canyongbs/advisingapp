<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Case\Models\CaseItemPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource;

test('The correct details are displayed on the ViewCaseItemPriority page', function () {
    $caseItemPriority = CaseItemPriority::factory()->create();

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

    $prospectSource = CaseItemPriority::factory()->create();

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
