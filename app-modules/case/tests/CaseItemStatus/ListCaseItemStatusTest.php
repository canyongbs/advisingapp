<?php

use Assist\Case\Models\CaseItem;

use function Pest\Livewire\livewire;

use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\ListCaseItemStatuses;

test('The correct details are displayed on the ListCaseItemStatus page', function () {
    $caseItemStatuses = CaseItemStatus::factory()
        ->has(CaseItem::factory()->count(fake()->randomNumber(1)), 'caseItems')
        ->count(10)
        ->create();

    $component = livewire(ListCaseItemStatuses::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($caseItemStatuses)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('case_items_count');

    $caseItemStatuses->each(
        fn (CaseItemStatus $caseItemType) => $component
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
