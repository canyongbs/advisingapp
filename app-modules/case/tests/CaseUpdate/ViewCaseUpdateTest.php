<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequestUpdate;
use Assist\Case\Filament\Resources\CaseUpdateResource;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

test('The correct details are displayed on the ViewCaseUpdate page', function () {
    $caseItemUpdate = ServiceRequestUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            CaseUpdateResource::getUrl('view', [
                'record' => $caseItemUpdate,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Case',
                $caseItemUpdate->case->casenumber,
                'Internal',
                // TODO: Figure out how to check whether this internal value the check or the X icon
                'Direction',
                $caseItemUpdate->direction->name,
                'Update',
                $caseItemUpdate->update,
            ]
        );
});

// Permission Tests

test('ViewCaseUpdate is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseUpdate = ServiceRequestUpdate::factory()->create();

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('view', [
                'record' => $caseUpdate,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_update.view-any');
    $user->givePermissionTo('case_update.*.view');

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('view', [
                'record' => $caseUpdate,
            ])
        )->assertSuccessful();
});
