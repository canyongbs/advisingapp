<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionTypeResource;

test('CreateInteractionType is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionTypeResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction_type.view-any');
    $user->givePermissionTo('interaction_type.create');

    actingAs($user)
        ->get(
            InteractionTypeResource::getUrl('create')
        )->assertSuccessful();
});
