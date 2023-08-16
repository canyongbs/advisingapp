<?php

use App\Models\User;

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseUpdate;

use function Pest\Laravel\actingAs;

use Assist\Case\Filament\Resources\CaseUpdateResource;

test('The correct details are displayed on the ViewCaseUpdate page', function () {
    $caseItemUpdate = CaseUpdate::factory()->create();

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

    $caseUpdate = CaseUpdate::factory()->create();

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
