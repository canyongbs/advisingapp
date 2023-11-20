<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionStatusResource;

test('ListInteractionStatuses is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction_status.view-any');

    actingAs($user)
        ->get(
            InteractionStatusResource::getUrl('index')
        )->assertSuccessful();
});
