<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\EditCaseType;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Tests\Tenant\RequestFactories\EditCaseTypeRequestFactory;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditCaseType page', function () {
    $caseType = CaseType::factory()->create();

    asSuperAdmin()
        ->get(
            CaseTypeResource::getUrl('edit', [
                'record' => $caseType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCaseTypeRequestFactory::new()->create();

    livewire(EditCaseType::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseType->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $caseType->fresh()->name);
});

test('EditCaseType requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $caseType = CaseType::factory()->create();

    livewire(EditCaseType::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $caseType->name,
        ])
        ->fillForm(EditCaseTypeRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CaseType::class, $caseType->toArray());
})->with(
    [
        'name missing' => [EditCaseTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditCaseTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditCaseType is gated with proper access control', function () {
    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    $caseType = CaseType::factory()->create();

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('edit', [
                'record' => $caseType,
            ])
        )->assertForbidden();

    livewire(EditCaseType::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('edit', [
                'record' => $caseType,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseTypeRequestFactory::new()->create());

    livewire(EditCaseType::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $caseType->fresh()->name);
});

test('EditCaseType is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    $caseType = CaseType::factory()->create();

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('edit', [
                'record' => $caseType,
            ])
        )->assertForbidden();

    livewire(EditCaseType::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->caseManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('edit', [
                'record' => $caseType,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseTypeRequestFactory::new()->create());

    livewire(EditCaseType::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $caseType->fresh()->name);
});
