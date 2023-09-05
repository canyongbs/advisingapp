<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Filament\Resources\ProspectSourceResource;
use Assist\Prospect\Filament\Resources\ProspectSourceResource\Pages\ListProspectSources;

test('The correct details are displayed on the ListProspectSources page', function () {
    $prospectSources = ProspectSource::factory()
        // TODO: Fix this once Prospect factory is created
        //->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'caseItems')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListProspectSources::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($prospectSources)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('prospects_count');

    $prospectSources->each(
        fn (ProspectSource $prospectSource) => $component
            ->assertTableColumnStateSet(
                'id',
                $prospectSource->id,
                $prospectSource
            )
            ->assertTableColumnStateSet(
                'name',
                $prospectSource->name,
                $prospectSource
            )
        // Currently setting not test for case_items_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListProspectSources is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect_source.view-any');

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('index')
        )->assertSuccessful();
});
