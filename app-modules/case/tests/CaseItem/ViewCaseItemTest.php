<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Filament\Resources\CaseItemResource;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

test('The correct details are displayed on the ViewCaseItem page', function () {
    $caseItem = ServiceRequest::factory()->create();

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

    $caseItem = ServiceRequest::factory()->create();

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
