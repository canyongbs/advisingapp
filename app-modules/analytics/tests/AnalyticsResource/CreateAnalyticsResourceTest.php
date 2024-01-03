<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

use AdvisingApp\Analytics\Models\AnalyticsResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages\CreateAnalyticsResource;

test('CreateAnalyticsResourceTest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateAnalyticsResource::class)
        ->assertForbidden();

    $user->givePermissionTo('analytics_resource.view-any');
    $user->givePermissionTo('analytics_resource.create');

    actingAs($user)
        ->get(
            AnalyticsResourceResource::getUrl('create')
        )->assertSuccessful();

    /** @var AnalyticsResource $request */
    $request = AnalyticsResource::factory()->make();

    livewire(CreateAnalyticsResource::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, AnalyticsResource::all());

    /** @var AnalyticsResource $analyticsResource */
    $analyticsResource = AnalyticsResource::first();

    expect($analyticsResource)
        ->name->toBe($request->name)
        ->description->toBe($request->description)
        ->url->toBe($request->url)
        ->is_active->toBe($request->is_active)
        ->is_included_in_data_portal->toBe($request->is_included_in_data_portal)
        ->source_id->toBe($request->source_id)
        ->category_id->toBe($request->category_id);
});
