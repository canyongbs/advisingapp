<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Filament\Resources\InteractionDriverResource;

test('EditInteractionDriver is gated with proper access control', function () {
    $user = User::factory()->create();

    $driver = InteractionDriver::factory()->create();

    actingAs($user)
        ->get(
            InteractionDriverResource::getUrl('edit', ['record' => $driver])
        )->assertForbidden();

    $user->givePermissionTo('interaction_driver.view-any');
    $user->givePermissionTo('interaction_driver.*.update');

    actingAs($user)
        ->get(
            InteractionDriverResource::getUrl('edit', ['record' => $driver])
        )->assertSuccessful();
});
