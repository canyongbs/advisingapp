<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource;

test('ListAnalyticsResourceCategoriesTest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl()
        )->assertForbidden();

    $user->givePermissionTo('analytics_resource_category.view-any');

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl()
        )->assertSuccessful();
});
