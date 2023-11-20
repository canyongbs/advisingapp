<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Audit\Filament\Resources\AuditResource;

// TODO: Write tests for the ListAudits page
//test('The correct details are displayed on the ListAudits page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListAudits is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AuditResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('audit.view-any');

    actingAs($user)
        ->get(
            AuditResource::getUrl('index')
        )->assertSuccessful();
});
