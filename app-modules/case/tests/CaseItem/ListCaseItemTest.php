<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Filament\Resources\ServiceRequestResource;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\ListCaseItems;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('The correct details are displayed on the ListCaseItem page', function () {
    $caseItems = ServiceRequest::factory()
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListCaseItems::class);

    $component->assertSuccessful()
        ->assertCanSeeTableRecords($caseItems)
        ->assertCountTableRecords(10);

    $caseItems->each(
        fn (ServiceRequest $caseItem) => $component
            ->assertTableColumnStateSet(
                'id',
                $caseItem->id,
                $caseItem
            )
            ->assertTableColumnStateSet(
                'casenumber',
                $caseItem->casenumber,
                $caseItem
            )
            ->assertTableColumnStateSet(
                'respondent.full',
                $caseItem->respondent->full,
                $caseItem
            )
            ->assertTableColumnStateSet(
                'respondent.sisid',
                $caseItem->respondent->sisid,
                $caseItem
            )
            ->assertTableColumnStateSet(
                'respondent.otherid',
                $caseItem->respondent->otherid,
                $caseItem
            )
            ->assertTableColumnStateSet(
                'institution.name',
                $caseItem->institution->name,
                $caseItem
            )
            ->assertTableColumnStateSet(
                'assignedTo.name',
                $caseItem->assignedTo->name,
                $caseItem
            )
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListCaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('case_item.view-any');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertSuccessful();
});
