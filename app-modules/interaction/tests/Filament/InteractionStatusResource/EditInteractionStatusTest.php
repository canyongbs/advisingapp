<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Filament\Resources\InteractionStatusResource;

test('EditInteractionStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $status = InteractionStatus::factory()->create();

    actingAs($user)
        ->get(
            InteractionStatusResource::getUrl('edit', ['record' => $status])
        )->assertForbidden();

    $user->givePermissionTo('interaction_status.view-any');
    $user->givePermissionTo('interaction_status.*.update');

    actingAs($user)
        ->get(
            InteractionStatusResource::getUrl('edit', ['record' => $status])
        )->assertSuccessful();
});
