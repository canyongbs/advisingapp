<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

test('CreateCaseload is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            CaseloadResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('caseload.view-any');
    $user->givePermissionTo('caseload.create');

    actingAs($user)
        ->get(
            CaseloadResource::getUrl('create')
        )->assertSuccessful();
});
