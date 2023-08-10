<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages\ListProspectStatuses;

test('The correct details are displayed on the ListProspectStatuses page', function () {
    $prospectStatuses = ProspectStatus::factory()
        // TODO: Fix this once Prospect factory is created
        //->has(CaseItem::factory()->count(fake()->randomNumber(1)), 'caseItems')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListProspectStatuses::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($prospectStatuses)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('prospects_count');

    $prospectStatuses->each(
        fn (ProspectStatus $prospectStatus) => $component
            ->assertTableColumnStateSet(
                'id',
                $prospectStatus->id,
                $prospectStatus
            )
            ->assertTableColumnStateSet(
                'name',
                $prospectStatus->name,
                $prospectStatus
            )
            ->assertTableColumnStateSet(
                'color',
                $prospectStatus->color,
                $prospectStatus
            )
        // Currently setting not test for case_items_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListProspectStatuses is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect_status.view-any');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('index')
        )->assertSuccessful();
});
