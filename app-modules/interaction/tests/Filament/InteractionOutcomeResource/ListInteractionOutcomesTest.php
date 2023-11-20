<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionOutcomeResource;

test('ListInteractionOutcomes is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionOutcomeResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction_outcome.view-any');

    actingAs($user)
        ->get(
            InteractionOutcomeResource::getUrl('index')
        )->assertSuccessful();
});
