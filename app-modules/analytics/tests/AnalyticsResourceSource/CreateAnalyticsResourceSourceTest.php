<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use AdvisingApp\Analytics\Models\AnalyticsResourceSource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages\CreateAnalyticsResourceSource;

test('CreateAnalyticsResourceSourceTest is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateAnalyticsResourceSource::class)
        ->assertForbidden();

    $user->givePermissionTo('analytics_resource_source.view-any');
    $user->givePermissionTo('analytics_resource_source.create');

    actingAs($user)
        ->get(
            AnalyticsResourceSourceResource::getUrl('create')
        )->assertSuccessful();

    $request = AnalyticsResourceSource::factory()->make();

    livewire(CreateAnalyticsResourceSource::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, AnalyticsResourceSource::all());

    assertDatabaseHas(AnalyticsResourceSource::class, $request->toArray());
});
