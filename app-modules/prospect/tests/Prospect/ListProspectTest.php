<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Prospect\Filament\Resources\ProspectResource;

// TODO: Write ListProspects page test
//test('The correct details are displayed on the ListProspects page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListProspects is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ProspectResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect.view-any');

    actingAs($user)
        ->get(
            ProspectResource::getUrl('index')
        )->assertSuccessful();
});
