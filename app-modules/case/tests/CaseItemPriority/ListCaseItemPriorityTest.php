<?php

use Assist\Case\Models\CaseItem;

use function Pest\Livewire\livewire;

use Assist\Case\Models\CaseItemPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages\ListCaseItemPriorities;

test('The correct details are displayed on the ListCaseItemPriority page', function () {
    $caseItemTypes = CaseItemPriority::factory()
        ->has(CaseItem::factory()->count(fake()->randomNumber(1)), 'caseItems')
        ->count(3)
        ->sequence(
            ['name' => 'High', 'order' => 1],
            ['name' => 'Medium', 'order' => 2],
            ['name' => 'Low', 'order' => 3],
        )
        ->create();

    $component = livewire(ListCaseItemPriorities::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($caseItemTypes)
        ->assertCountTableRecords(3)
        ->assertTableColumnExists('case_items_count');

    $caseItemTypes->each(
        fn (CaseItemPriority $caseItemType) => $component
            ->assertTableColumnStateSet(
                'name',
                $caseItemType->name,
                $caseItemType
            )
            ->assertTableColumnStateSet(
                'order',
                $caseItemType->order,
                $caseItemType
            )
        // Currently setting not test for case_items_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests
