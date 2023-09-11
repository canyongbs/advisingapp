<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\InteractionRelation;
use Assist\Interaction\Filament\Resources\InteractionRelationResource;

test('EditInteractionRelation is gated with proper access control', function () {
    $user = User::factory()->create();

    $relation = InteractionRelation::factory()->create();

    actingAs($user)
        ->get(
            InteractionRelationResource::getUrl('edit', ['record' => $relation])
        )->assertForbidden();

    $user->givePermissionTo('interaction_relation.view-any');
    $user->givePermissionTo('interaction_relation.*.update');

    actingAs($user)
        ->get(
            InteractionRelationResource::getUrl('edit', ['record' => $relation])
        )->assertSuccessful();
});
