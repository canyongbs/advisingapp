<?php

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource;

test('The correct details are displayed on the ViewCaseItemStatus page', function () {
    $caseItemStatus = CaseItemStatus::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemStatusResource::getUrl('view', [
                'record' => $caseItemStatus,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $caseItemStatus->id,
                'Name',
                $caseItemStatus->name,
                'Color',
                $caseItemStatus->color,
            ]
        );
});
