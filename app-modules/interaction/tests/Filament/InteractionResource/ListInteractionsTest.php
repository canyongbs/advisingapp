<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionResource;

test('ListInteractions is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction.view-any');

    actingAs($user)
        ->get(
            InteractionResource::getUrl('index')
        )->assertSuccessful();
});
