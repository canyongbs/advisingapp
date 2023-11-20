<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Division\Models\Division;
use Assist\Division\Filament\Resources\DivisionResource;

test('EditDivision is gated with proper access control', function () {
    $user = User::factory()->create();

    $division = Division::factory()->create();

    actingAs($user)
        ->get(
            DivisionResource::getUrl('edit', ['record' => $division])
        )->assertForbidden();

    $user->givePermissionTo('division.view-any');
    $user->givePermissionTo('division.*.update');

    actingAs($user)
        ->get(
            DivisionResource::getUrl('edit', ['record' => $division])
        )->assertSuccessful();
});
