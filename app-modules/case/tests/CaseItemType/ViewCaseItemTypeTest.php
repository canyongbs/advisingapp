<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Case\Models\CaseItemType;
use Assist\Case\Filament\Resources\CaseItemTypeResource;

test('The correct details are displayed on the ViewCaseItemType page', function () {
    $caseItemType = CaseItemType::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemTypeResource::getUrl('view', [
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

test('ViewCaseItemType is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseItemType = CaseItemType::factory()->create();

    actingAs($user)
        ->get(
            CaseItemTypeResource::getUrl('view', [
                'record' => $caseItemType,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_item_type.view-any');
    $user->givePermissionTo('case_item_type.*.view');

    actingAs($user)
        ->get(
            CaseItemTypeResource::getUrl('view', [
                'record' => $caseItemType,
            ])
        )->assertSuccessful();
});
