<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionTypeResource;

test('ListInteractionTypes is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionTypeResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction_type.view-any');

    actingAs($user)
        ->get(
            InteractionTypeResource::getUrl('index')
        )->assertSuccessful();
});
