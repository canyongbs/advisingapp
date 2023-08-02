<?php

use Assist\Case\Models\CaseItem;
use Assist\Case\Models\CaseItemType;

use function Pest\Livewire\livewire;

use Assist\Case\Filament\Resources\CaseItemTypeResource\Pages\ListCaseItemTypes;

test('The correct details are displayed on the ListCaseItemType page', function () {
    $caseItemTypes = CaseItemType::factory()
        ->has(CaseItem::factory()->count(fake()->randomNumber(1)), 'caseItems')
        ->count(10)
        ->create();

    $component = livewire(ListCaseItemTypes::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($caseItemTypes)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('case_items_count');

    $caseItemTypes->each(
        fn (CaseItemType $caseItemType) => $component
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
        // Currently setting not test for case_items_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests
