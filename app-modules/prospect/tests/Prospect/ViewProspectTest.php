<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Prospect\Models\Prospect;
use Assist\Prospect\Filament\Resources\ProspectResource;

// TODO: Write ViewProspectSource page test
//test('The correct details are displayed on the ViewProspect page', function () {});

// Permission Tests

test('ViewProspect is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospect = Prospect::factory()->create();

    actingAs($user)
        ->get(
            ProspectResource::getUrl('view', [
                'record' => $prospect,
            ])
        )->assertForbidden();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user)
        ->get(
            ProspectResource::getUrl('view', [
                'record' => $prospect,
            ])
        )->assertSuccessful();
});
