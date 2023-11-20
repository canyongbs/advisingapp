<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionRelationResource;

test('ListInteractionRelations is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionRelationResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction_relation.view-any');

    actingAs($user)
        ->get(
            InteractionRelationResource::getUrl('index')
        )->assertSuccessful();
});
