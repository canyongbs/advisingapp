<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AdvisingApp\Analytics\Models\AnalyticsResourceSource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;

test('ViewAnalyticsResourceCategoryTest is gated with proper access control', function () {
    $user = User::factory()->create();

    $analyticsResourceSource = AnalyticsResourceSource::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl('view', [
                'record' => $analyticsResourceSource,
            ])
        )->assertForbidden();

    $user->givePermissionTo('analytics_resource_source.view-any');
    $user->givePermissionTo('analytics_resource_source.*.view');

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl('view', [
                'record' => $analyticsResourceSource,
            ])
        )->assertSuccessful();
});
