<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource;
use Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages\ListCaseItemPriorities;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('The correct details are displayed on the ListCaseItemPriority page', function () {
    $caseItemTypes = ServiceRequestPriority::factory()
        ->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'caseItems')
        ->count(3)
        ->sequence(
            ['name' => 'High', 'order' => 1],
            ['name' => 'Medium', 'order' => 2],
            ['name' => 'Low', 'order' => 3],
        )
        ->create();

    asSuperAdmin();

    $component = livewire(ListCaseItemPriorities::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($caseItemTypes)
        ->assertCountTableRecords(3)
        ->assertTableColumnExists('case_items_count');

    $caseItemTypes->each(
        fn (ServiceRequestPriority $caseItemType) => $component
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

// Permission Tests

test('ListServiceRequestPriorities is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('case_item_priority.view-any');

    actingAs($user)
        ->get(
            ServiceRequestPriorityResource::getUrl('index')
        )->assertSuccessful();
});
