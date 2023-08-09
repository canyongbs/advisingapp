<?php

use function Tests\asSuperAdmin;

use Assist\Case\Models\CaseUpdate;
use Assist\Case\Filament\Resources\CaseUpdateResource;

test('The correct details are displayed on the ViewCaseUpdate page', function () {
    $caseItemUpdate = CaseUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            CaseUpdateResource::getUrl('view', [
                'record' => $caseItemUpdate,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Case',
                $caseItemUpdate->case->casenumber,
                'Internal',
                // TODO: Figure out how to check whether this internal value the check or the X icon
                'Direction',
                $caseItemUpdate->direction->name,
                'Update',
                $caseItemUpdate->update,
            ]
        );
});
