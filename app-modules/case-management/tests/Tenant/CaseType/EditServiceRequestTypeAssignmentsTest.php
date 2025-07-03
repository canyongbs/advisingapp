<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\EditServiceRequestTypeAssignments;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Rules\ServiceRequestTypeAssignmentsIndividualUserMustBeAManager;
use AidingApp\ServiceManagement\Tests\RequestFactories\EditServiceRequestTypeAssignmentsRequestFactory;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditServiceRequestTypeAssignments page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertFormSet([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::None->value,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['assignment_type'], $serviceRequestType->fresh()->assignment_type->value);
});

test('A successful action on the EditServiceRequestTypeAssignments page when the type selected is Individual', function () {
    $managerTeam = Team::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()
        ->hasAttached(
            factory: $managerTeam,
            relationship: 'managers'
        )
        ->create();

    asSuperAdmin()
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestTypeAssignmentsRequestFactory::new()
        ->withIndividualType()
        ->withIndividualId($managerTeam)
        ->create();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['assignment_type'], $serviceRequestType->fresh()->assignment_type->value);
});

test('EditServiceRequestTypeAssignments requires valid data', function (EditServiceRequestTypeAssignmentsRequestFactory $data, $errors) {
    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestType::class, $serviceRequestType->toArray());
})->with(
    [
        'assignment_type is required' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->state(['assignment_type' => null]), ['assignment_type' => 'required']],
        'assignment_type is not a valid enum value' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->state(['assignment_type' => 'blah']), ['assignment_type' => Enum::class]],
        'assignment_type_individual_id is required when assignment_type is Individual' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->withIndividualType()->state(['assignment_type_individual_id' => null]), ['assignment_type_individual_id' => 'required']],
        'assignment_type_individual_id must be a User in the ServiceRequestTypes managers' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->withIndividualType()->state(['assignment_type_individual_id' => User::factory()]), ['assignment_type_individual_id' => ServiceRequestTypeAssignmentsIndividualUserMustBeAManager::class]],
    ]
);

// Permission Tests

test('EditServiceRequestTypeAssignments is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create());

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['assignment_type'], $serviceRequestType->fresh()->assignment_type->value);
});

test('EditServiceRequestTypeAssignments is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create());

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['assignment_type'], $serviceRequestType->fresh()->assignment_type->value);
});
