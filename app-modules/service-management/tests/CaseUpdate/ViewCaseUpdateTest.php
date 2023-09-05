<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;

test('The correct details are displayed on the ViewServiceRequestUpdate page', function () {
    $caseItemUpdate = ServiceRequestUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
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

test('ViewServiceRequestUpdate is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseUpdate = ServiceRequestUpdate::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $caseUpdate,
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_update.view-any');
    $user->givePermissionTo('case_update.*.view');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $caseUpdate,
            ])
        )->assertSuccessful();
});
