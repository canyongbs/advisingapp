<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionDriverResource;

test('CreateInteractionDriver is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionDriverResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction_driver.view-any');
    $user->givePermissionTo('interaction_driver.create');

    actingAs($user)
        ->get(
            InteractionDriverResource::getUrl('create')
        )->assertSuccessful();
});
