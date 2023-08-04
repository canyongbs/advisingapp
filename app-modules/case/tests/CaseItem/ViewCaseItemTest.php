<?php

use Assist\Case\Models\CaseItem;

use function Tests\asSuperAdmin;

use Assist\Case\Filament\Resources\CaseItemResource;

test('The correct details are displayed on the ViewCaseItem page', function () {
    $caseItem = CaseItem::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemResource::getUrl('view', [
                'record' => $caseItem,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $caseItem->id,
                'Case Number',
                $caseItem->casenumber,
                'Institution',
                $caseItem->institution->name,
                'State',
                $caseItem->state->name,
                'Priority',
                $caseItem->priority->name,
                'Type',
                $caseItem->type->name,
                'Close Details/Description',
                $caseItem->close_details,
                'Internal Case Details',
                $caseItem->res_details,
            ]
        );
});
