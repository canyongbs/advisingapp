<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\EditCaseTypeAssignments;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Rules\CaseTypeAssignmentsIndividualUserMustBeAManager;
use AdvisingApp\CaseManagement\Tests\Tenant\RequestFactories\EditCaseTypeAssignmentsRequestFactory;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Validation\Rules\Enum;

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
            'assignment_type' => CaseTypeAssignmentTypes::None->value,
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
        'assignment_type is not a valid enum value' => [EditCaseTypeAssignmentsRequestFactory::new()->state(['assignment_type' => 'blah']), ['assignment_type' => Enum::class]],
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

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

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

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

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
