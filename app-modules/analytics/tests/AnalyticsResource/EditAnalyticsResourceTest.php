<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Analytics\Models\AnalyticsResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages\EditAnalyticsResource;

test('EditAnalyticsResourceTest is gated with proper access control', function () {
    $user = User::factory()->create();

    $analyticsResource = AnalyticsResource::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceResource::getUrl('edit', [
                'record' => $analyticsResource,
            ])
        )->assertForbidden();

    livewire(EditAnalyticsResource::class, [
        'record' => $analyticsResource->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('analytics_resource.view-any');
    $user->givePermissionTo('analytics_resource.*.update');

    actingAs($user)
        ->get(
            AnalyticsResourceResource::getUrl('edit', [
                'record' => $analyticsResource,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    /** @var AnalyticsResource $request */
    $request = AnalyticsResource::factory()->make();

    livewire(EditAnalyticsResource::class, [
        'record' => $analyticsResource->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    /** @var AnalyticsResource $analyticsResource */
    expect($analyticsResource->refresh())
        ->name->toBe($request->name)
        ->description->toBe($request->description)
        ->url->toBe($request->url)
        ->is_active->toBe($request->is_active)
        ->is_included_in_data_portal->toBe($request->is_included_in_data_portal)
        ->source_id->toBe($request->source_id)
        ->category_id->toBe($request->category_id);
});
