<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\InteractionInstitution;
use Assist\Interaction\Filament\Resources\InteractionInstitutionResource;

test('EditInteractionInstitution is gated with proper access control', function () {
    $user = User::factory()->create();

    $institution = InteractionInstitution::factory()->create();

    actingAs($user)
        ->get(
            InteractionInstitutionResource::getUrl('edit', ['record' => $institution])
        )->assertForbidden();

    $user->givePermissionTo('interaction_institution.view-any');
    $user->givePermissionTo('interaction_institution.*.update');

    actingAs($user)
        ->get(
            InteractionInstitutionResource::getUrl('edit', ['record' => $institution])
        )->assertSuccessful();
});
