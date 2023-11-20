<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Filament\Resources\InteractionOutcomeResource;

test('EditInteractionOutcome is gated with proper access control', function () {
    $user = User::factory()->create();

    $outcome = InteractionOutcome::factory()->create();

    actingAs($user)
        ->get(
            InteractionOutcomeResource::getUrl('edit', ['record' => $outcome])
        )->assertForbidden();

    $user->givePermissionTo('interaction_outcome.view-any');
    $user->givePermissionTo('interaction_outcome.*.update');

    actingAs($user)
        ->get(
            InteractionOutcomeResource::getUrl('edit', ['record' => $outcome])
        )->assertSuccessful();
});
