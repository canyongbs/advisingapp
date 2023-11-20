<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionDriverResource;

test('ListInteractionDrivers is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionDriverResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction_driver.view-any');

    actingAs($user)
        ->get(
            InteractionDriverResource::getUrl('index')
        )->assertSuccessful();
});
