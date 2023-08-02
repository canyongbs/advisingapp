<?php

use function Tests\asSuperAdmin;
use function Pest\Laravel\artisan;

use Assist\Case\Models\CaseItemPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource;

test('The correct details are displayed on the ViewCaseItemPriority page', function () {
    artisan('roles-and-permissions:sync');

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
