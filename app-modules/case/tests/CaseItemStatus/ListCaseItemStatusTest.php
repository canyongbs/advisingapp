<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Models\ServiceRequestStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\ListCaseItemStatuses;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('The correct details are displayed on the ListCaseItemStatus page', function () {
    $caseItemStatuses = ServiceRequestStatus::factory()
        ->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'caseItems')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListCaseItemStatuses::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($caseItemStatuses)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('case_items_count');

    $caseItemStatuses->each(
        fn (ServiceRequestStatus $caseItemType) => $component
            ->assertTableColumnStateSet(
                'id',
                $caseItemType->id,
                $caseItemType
            )
            ->assertTableColumnStateSet(
                'name',
                $caseItemType->name,
                $caseItemType
            )
            ->assertTableColumnStateSet(
                'color',
                $caseItemType->color,
                $caseItemType
            )
        // Currently setting not test for case_items_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListCaseItemStatuses is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            CaseItemStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('case_item_status.view-any');

    actingAs($user)
        ->get(
            CaseItemStatusResource::getUrl('index')
        )->assertSuccessful();
});
