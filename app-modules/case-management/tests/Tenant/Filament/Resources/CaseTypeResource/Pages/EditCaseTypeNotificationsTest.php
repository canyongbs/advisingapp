<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\EditCaseTypeNotifications;
use AdvisingApp\CaseManagement\Models\CaseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('EditCaseTypeNotifications is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $caseType = CaseType::factory()->create();

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('case-type-notifications', [
                'record' => $caseType,
            ])
        )->assertForbidden();

    livewire(EditCaseTypeNotifications::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('case-type-notifications', [
                'record' => $caseType,
            ])
        )->assertSuccessful();

    livewire(EditCaseTypeNotifications::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertSuccessful();
});
