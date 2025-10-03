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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\EditCaseTypeAssignments;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Rules\CaseTypeAssignmentsIndividualUserMustBeAManager;
use AdvisingApp\CaseManagement\Tests\Tenant\RequestFactories\EditCaseTypeAssignmentsRequestFactory;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditCaseTypeAssignments page', function () {
    $caseType = CaseType::factory()->create();

    asSuperAdmin()
        ->get(
            EditCaseTypeAssignments::getUrl([
                'record' => $caseType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCaseTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create();

    livewire(EditCaseTypeAssignments::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertFormSet([
            'assignment_type' => CaseTypeAssignmentTypes::None,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['assignment_type'], $caseType->fresh()->assignment_type->value);
});

test('A successful action on the EditCaseTypeAssignments page when the type selected is Individual', function () {
    $managerTeam = Team::factory()->create();

    $caseType = CaseType::factory()
        ->hasAttached(
            factory: $managerTeam,
            relationship: 'managers'
        )
        ->create();

    asSuperAdmin()
        ->get(
            EditCaseTypeAssignments::getUrl([
                'record' => $caseType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCaseTypeAssignmentsRequestFactory::new()
        ->withIndividualType()
        ->withIndividualId($managerTeam)
        ->create();

    livewire(EditCaseTypeAssignments::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['assignment_type'], $caseType->fresh()->assignment_type->value);
});

test('EditCaseTypeAssignments requires valid data', function (EditCaseTypeAssignmentsRequestFactory $data, array $errors) {
    asSuperAdmin();

    $caseType = CaseType::factory()->create();

    livewire(EditCaseTypeAssignments::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CaseType::class, $caseType->toArray());
})->with(
    [
        'assignment_type is required' => [EditCaseTypeAssignmentsRequestFactory::new()->state(['assignment_type' => null]), ['assignment_type' => 'required']],
        'assignment_type is not a valid enum value' => [EditCaseTypeAssignmentsRequestFactory::new()->state(['assignment_type' => 'blah']), ['assignment_type']],
        'assignment_type_individual_id is required when assignment_type is Individual' => [EditCaseTypeAssignmentsRequestFactory::new()->withIndividualType()->state(['assignment_type_individual_id' => null]), ['assignment_type_individual_id' => 'required']],
        'assignment_type_individual_id must be a User in the CaseTypes managers' => [EditCaseTypeAssignmentsRequestFactory::new()->withIndividualType()->state(['assignment_type_individual_id' => User::factory()]), ['assignment_type_individual_id' => CaseTypeAssignmentsIndividualUserMustBeAManager::class]],
    ]
);

test('EditCaseTypeAssignments is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $caseType = CaseType::factory()->create();

    actingAs($user)
        ->get(
            EditCaseTypeAssignments::getUrl([
                'record' => $caseType,
            ])
        )->assertForbidden();

    livewire(EditCaseTypeAssignments::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            EditCaseTypeAssignments::getUrl([
                'record' => $caseType,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create());

    livewire(EditCaseTypeAssignments::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['assignment_type'], $caseType->fresh()->assignment_type->value);
});

test('EditCaseTypeAssignments is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    $caseType = CaseType::factory()->create();

    actingAs($user)
        ->get(
            EditCaseTypeAssignments::getUrl([
                'record' => $caseType,
            ])
        )->assertForbidden();

    livewire(EditCaseTypeAssignments::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->caseManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            EditCaseTypeAssignments::getUrl([
                'record' => $caseType,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create());

    livewire(EditCaseTypeAssignments::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['assignment_type'], $caseType->fresh()->assignment_type->value);
});
