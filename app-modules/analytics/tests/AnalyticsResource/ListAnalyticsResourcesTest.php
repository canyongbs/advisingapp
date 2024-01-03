<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource;

test('ListAnalyticsResourcesTest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceResource::getUrl()
        )->assertForbidden();

    $user->givePermissionTo('analytics_resource.view-any');

    actingAs($user)
        ->get(
            AnalyticsResourceResource::getUrl()
        )->assertSuccessful();
});
