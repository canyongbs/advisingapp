<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionOutcomeResource;

test('CreateInteractionOutcome is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionOutcomeResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction_outcome.view-any');
    $user->givePermissionTo('interaction_outcome.create');

    actingAs($user)
        ->get(
            InteractionOutcomeResource::getUrl('create')
        )->assertSuccessful();
});
