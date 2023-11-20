<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Division\Filament\Resources\DivisionResource;

test('CreateDivision is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            DivisionResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('division.view-any');
    $user->givePermissionTo('division.create');

    actingAs($user)
        ->get(
            DivisionResource::getUrl('create')
        )->assertSuccessful();
});
