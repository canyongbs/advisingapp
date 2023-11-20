<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

test('EditCaseload is gated with proper access control', function () {
    $user = User::factory()->create();

    $caseload = Caseload::factory()->create();

    actingAs($user)
        ->get(
            CaseloadResource::getUrl('edit', ['record' => $caseload])
        )->assertForbidden();

    $user->givePermissionTo('caseload.view-any');
    $user->givePermissionTo('caseload.*.update');

    actingAs($user)
        ->get(
            CaseloadResource::getUrl('edit', ['record' => $caseload])
        )->assertSuccessful();
});
