<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionInstitutionResource;

test('CreateInteractionInstitution is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionInstitutionResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction_institution.view-any');
    $user->givePermissionTo('interaction_institution.create');

    actingAs($user)
        ->get(
            InteractionInstitutionResource::getUrl('create')
        )->assertSuccessful();
});
