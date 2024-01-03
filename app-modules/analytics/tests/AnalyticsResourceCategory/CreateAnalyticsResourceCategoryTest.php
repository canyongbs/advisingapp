<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages\CreateAnalyticsResourceCategory;

test('CreateAnalyticsResourceCategoryTest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateAnalyticsResourceCategory::class)
        ->assertForbidden();

    $user->givePermissionTo('analytics_resource_category.view-any');
    $user->givePermissionTo('analytics_resource_category.create');

    actingAs($user)
        ->get(
            AnalyticsResourceCategoryResource::getUrl('create')
        )->assertSuccessful();

    $request = AnalyticsResourceCategory::factory()->make();

    livewire(CreateAnalyticsResourceCategory::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, AnalyticsResourceCategory::all());

    assertDatabaseHas(AnalyticsResourceCategory::class, $request->toArray());
});
