<?php

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseItemPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource;

test('The correct details are displayed on the ViewCaseItemPriority page', function () {
    $caseItemPriority = CaseItemPriority::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemPriorityResource::getUrl('view', [
                'record' => $caseItemPriority,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $caseItemPriority->name,
                'Order',
                $caseItemPriority->order,
            ]
        );
});
