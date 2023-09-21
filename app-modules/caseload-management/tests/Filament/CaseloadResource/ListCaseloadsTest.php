<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

test('ListCaseloads is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            CaseloadResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('caseload.view-any');

    actingAs($user)
        ->get(
            CaseloadResource::getUrl('index')
        )->assertSuccessful();
});
