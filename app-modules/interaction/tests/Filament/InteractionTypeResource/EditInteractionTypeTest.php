<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Filament\Resources\InteractionTypeResource;

test('EditInteractionType is gated with proper access control', function () {
    $user = User::factory()->create();

    $type = InteractionType::factory()->create();

    actingAs($user)
        ->get(
            InteractionTypeResource::getUrl('edit', ['record' => $type])
        )->assertForbidden();

    $user->givePermissionTo('interaction_type.view-any');
    $user->givePermissionTo('interaction_type.*.update');

    actingAs($user)
        ->get(
            InteractionTypeResource::getUrl('edit', ['record' => $type])
        )->assertSuccessful();
});
