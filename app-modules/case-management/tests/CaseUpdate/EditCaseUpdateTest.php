<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Models\CaseUpdate;
use AdvisingApp\CaseManagement\Filament\Resources\CaseUpdateResource;
use AdvisingApp\CaseManagement\Tests\RequestFactories\EditCaseUpdateRequestFactory;
use AdvisingApp\CaseManagement\Filament\Resources\CaseUpdateResource\Pages\EditCaseUpdate;

test('A successful action on the EditCaseUpdate page', function () {
    $caseUpdate = CaseUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditCaseUpdateRequestFactory::new()->create());

    livewire(EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(CaseUpdate::class, $request->except('case_model_id')->toArray());

    expect(CaseUpdate::first()->case->id)
        ->toEqual($request->get('case_model_id'));
});

test('EditCaseUpdate requires valid data', function ($data, $errors) {
    $caseUpdate = CaseUpdate::factory()->create();

    asSuperAdmin();

    livewire(EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm(EditCaseUpdateRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    unset($caseUpdate->case);

    assertDatabaseHas(CaseUpdate::class, $caseUpdate->toArray());

    expect(CaseUpdate::first()->case->id)
        ->toEqual($caseUpdate->case->id);
})->with(
    [
        'case missing' => [EditCaseUpdateRequestFactory::new()->state(['case_model_id' => null]), ['case_model_id' => 'required']],
        'case not existing case id' => [EditCaseUpdateRequestFactory::new()->state(['case_model_id' => fake()->uuid()]), ['case_model_id' => 'exists']],
        'update missing' => [EditCaseUpdateRequestFactory::new()->state(['update' => null]), ['update' => 'required']],
        'update is not a string' => [EditCaseUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'direction missing' => [EditCaseUpdateRequestFactory::new()->state(['direction' => null]), ['direction' => 'required']],
        'direction not a valid enum' => [EditCaseUpdateRequestFactory::new()->state(['direction' => 'invalid']), ['direction' => Enum::class]],
        'internal not a boolean' => [EditCaseUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);

// Permission Tests

test('EditCaseUpdate is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $caseUpdate = CaseUpdate::factory()->create();

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate,
            ])
        )->assertForbidden();

    livewire(EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('case_update.view-any');
    $user->givePermissionTo('case_update.*.update');

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseUpdateRequestFactory::new()->create());

    livewire(EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(CaseUpdate::class, $request->except('case_model_id')->toArray());

    expect(CaseUpdate::first()->case->id)
        ->toEqual($request->get('case_model_id'));
});

test('EditCaseUpdate is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('case_update.view-any');
    $user->givePermissionTo('case_update.*.update');

    $caseUpdate = CaseUpdate::factory()->create();

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate,
            ])
        )->assertForbidden();

    livewire(EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            CaseUpdateResource::getUrl('edit', [
                'record' => $caseUpdate,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseUpdateRequestFactory::new()->create());

    livewire(EditCaseUpdate::class, [
        'record' => $caseUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['update'], $caseUpdate->fresh()->update);
});
