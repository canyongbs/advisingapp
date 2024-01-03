<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages\EditAnalyticsResourceCategory;

test('EditAnalyticsResourceCategoryTest is gated with proper access control', function () {
    $user = User::factory()->create();

    $analyticsResourceCategory = AnalyticsResourceCategory::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl('edit', [
                'record' => $analyticsResourceCategory,
            ])
        )->assertForbidden();

    livewire(EditAnalyticsResourceCategory::class, [
        'record' => $analyticsResourceCategory->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('analytics_resource_category.view-any');
    $user->givePermissionTo('analytics_resource_category.*.update');

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl('edit', [
                'record' => $analyticsResourceCategory,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    /** @var AnalyticsResourceCategory $request */
    $request = AnalyticsResourceCategory::factory()->make();

    livewire(EditAnalyticsResourceCategory::class, [
        'record' => $analyticsResourceCategory->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    /** @var AnalyticsResourceCategory $analyticsResourceCategory */
    expect($analyticsResourceCategory->refresh())
        ->name->toBe($request->name)
        ->description->toBe($request->description)
        ->classification->toBe($request->classification);
});
