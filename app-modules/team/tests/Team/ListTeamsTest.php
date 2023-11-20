<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Team\Filament\Resources\TeamResource;

// Permission Tests

test('ListTeams is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            TeamResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('team.view-any');

    actingAs($user)
        ->get(
            TeamResource::getUrl('index')
        )->assertSuccessful();
});
