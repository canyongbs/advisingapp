<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Division\Filament\Resources\DivisionResource;

test('ListDivisions is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            DivisionResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('division.view-any');

    actingAs($user)
        ->get(
            DivisionResource::getUrl('index')
        )->assertSuccessful();
});
