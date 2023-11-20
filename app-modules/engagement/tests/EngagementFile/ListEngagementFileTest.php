<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Engagement\Filament\Resources\EngagementFileResource;

// TODO: Add tests for the ListEngagementFiles
//test('The correct details are displayed on the ListEngagementFiles page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListEngagementFiles is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('engagement_file.view-any');

    actingAs($user)
        ->get(
            EngagementFileResource::getUrl('index')
        )->assertSuccessful();
});
