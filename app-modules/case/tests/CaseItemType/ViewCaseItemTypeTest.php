<?php

use function Tests\asSuperAdmin;
use function Pest\Laravel\artisan;

use Assist\Case\Models\CaseItemType;
use Assist\Case\Filament\Resources\CaseItemTypeResource;

test('The correct details are displayed on the ViewCaseItemType page', function () {
    artisan('roles-and-permissions:sync');

    $caseItemType = CaseItemType::factory()->create();

    asSuperAdmin()
        ->get(
            CaseItemTypeResource::getUrl('view', [
                'record' => $caseItemType,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $caseItemType->id,
                'Name',
                $caseItemType->name,
            ]
        );
});
