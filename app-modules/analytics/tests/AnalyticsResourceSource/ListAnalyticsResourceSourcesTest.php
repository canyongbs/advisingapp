<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;

test('ListAnalyticsResourceSourcesTest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl()
        )->assertForbidden();

    $user->givePermissionTo('analytics_resource_source.view-any');

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl()
        )->assertSuccessful();
});
