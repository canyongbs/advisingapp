<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionResource;

test('CreateInteraction is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction.view-any');
    $user->givePermissionTo('interaction.create');

    actingAs($user)
        ->get(
            InteractionResource::getUrl('create')
        )->assertSuccessful();
});
