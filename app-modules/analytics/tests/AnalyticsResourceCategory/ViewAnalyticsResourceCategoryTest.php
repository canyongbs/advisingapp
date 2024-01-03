<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource;

test('ViewAnalyticsResourceCategoryTest is gated with proper access control', function () {
    $user = User::factory()->create();

    $analyticsResourceCategory = AnalyticsResourceCategory::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl('view', [
                'record' => $analyticsResourceCategory,
            ])
        )->assertForbidden();

    $user->givePermissionTo('analytics_resource_category.view-any');
    $user->givePermissionTo('analytics_resource_category.*.view');

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl('view', [
                'record' => $analyticsResourceCategory,
            ])
        )->assertSuccessful();
});
