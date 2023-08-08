<?php

use App\Models\User;
use Assist\Case\Models\CaseItem;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Case\Filament\Resources\CaseItemResource;

test('The correct details are displayed on the ViewCaseItem page', function () {
    $caseItem = CaseItem::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemResource::getUrl('view', [
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

test('ViewCaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseItem = CaseItem::factory()->create();

    actingAs($user)
        ->get(
            CaseItemResource::getUrl('view', [
                'record' => $caseItem,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_item.view-any');
    $user->givePermissionTo('case_item.*.view');

    actingAs($user)
        ->get(
            CaseItemResource::getUrl('view', [
                'record' => $caseItem,
            ])
        )->assertSuccessful();
});
