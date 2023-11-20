<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\Interaction;
use Assist\Interaction\Filament\Resources\InteractionResource;

test('EditInteraction is gated with proper access control', function () {
    $user = User::factory()->create();

    $interaction = Interaction::factory()->create();

    actingAs($user)
        ->get(
            InteractionResource::getUrl('edit', ['record' => $interaction])
        )->assertForbidden();

    $user->givePermissionTo('interaction.view-any');
    $user->givePermissionTo('interaction.*.update');

    actingAs($user)
        ->get(
            InteractionResource::getUrl('edit', ['record' => $interaction])
        )->assertSuccessful();
});
