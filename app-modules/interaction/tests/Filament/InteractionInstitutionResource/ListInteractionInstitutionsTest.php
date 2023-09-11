<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionInstitutionResource;

test('ListInteractionInstitutions is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionInstitutionResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction_institution.view-any');

    actingAs($user)
        ->get(
            InteractionInstitutionResource::getUrl('index')
        )->assertSuccessful();
});
