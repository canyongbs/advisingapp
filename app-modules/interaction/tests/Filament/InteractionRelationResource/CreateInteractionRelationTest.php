<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionRelationResource;

test('CreateInteractionRelation is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionRelationResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction_relation.view-any');
    $user->givePermissionTo('interaction_relation.create');

    actingAs($user)
        ->get(
            InteractionRelationResource::getUrl('create')
        )->assertSuccessful();
});
