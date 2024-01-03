<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Analytics\Models\AnalyticsResourceSource;
use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages\EditAnalyticsResourceSource;

test('EditAnalyticsResourceSourceTest is gated with proper access control', function () {
    $user = User::factory()->create();

    $analyticsResourceSource = AnalyticsResourceSource::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl('edit', [
                'record' => $analyticsResourceSource,
            ])
        )->assertForbidden();

    livewire(EditAnalyticsResourceSource::class, [
        'record' => $analyticsResourceSource->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('analytics_resource_source.view-any');
    $user->givePermissionTo('analytics_resource_source.*.update');

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl('edit', [
                'record' => $analyticsResourceSource,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    /** @var AnalyticsResourceSource $request */
    $request = AnalyticsResourceCategory::factory()->make();

    livewire(EditAnalyticsResourceSource::class, [
        'record' => $analyticsResourceSource->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    /** @var AnalyticsResourceSource $analyticsResourceSource */
    expect($analyticsResourceSource->refresh())
        ->name->toBe($request->name);
});
