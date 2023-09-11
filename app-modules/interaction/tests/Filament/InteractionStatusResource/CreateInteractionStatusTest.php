<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionStatusResource;

test('CreateInteractionStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionStatusResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction_status.view-any');
    $user->givePermissionTo('interaction_status.create');

    actingAs($user)
        ->get(
            InteractionStatusResource::getUrl('create')
        )->assertSuccessful();
});
