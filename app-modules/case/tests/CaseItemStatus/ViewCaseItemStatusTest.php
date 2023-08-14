<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource;

test('The correct details are displayed on the ViewCaseItemStatus page', function () {
    $caseItemStatus = CaseItemStatus::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemStatusResource::getUrl('view', [
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

test('ViewCaseItemStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectSource = CaseItemStatus::factory()->create();

    actingAs($user)
        ->get(
            CaseItemStatusResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_item_status.view-any');
    $user->givePermissionTo('case_item_status.*.view');

    actingAs($user)
        ->get(
            CaseItemStatusResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();
});
