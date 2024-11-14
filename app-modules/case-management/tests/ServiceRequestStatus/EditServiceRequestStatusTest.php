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
use AdvisingApp\Prospect\Models\Prospect;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\CaseManagement\Models\ServiceRequestStatus;
use AdvisingApp\CaseManagement\Filament\Resources\ServiceRequestStatusResource;
use AdvisingApp\CaseManagement\Tests\RequestFactories\EditServiceRequestStatusRequestFactory;
use AdvisingApp\CaseManagement\Filament\Resources\ServiceRequestStatusResource\Pages\EditServiceRequestStatus;

test('A successful action on the EditServiceRequestStatus page', function () {
    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestStatusRequestFactory::new()->create();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $serviceRequestStatus->classification->value,
            'name' => $serviceRequestStatus->name,
            'color' => $serviceRequestStatus->color->value,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $serviceRequestStatus->fresh()->name);
    assertEquals($editRequest['classification'], $serviceRequestStatus->fresh()->classification);
    assertEquals($editRequest['color'], $serviceRequestStatus->fresh()->color);
});

test('EditServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $serviceRequestStatus->classification->value,
            'name' => $serviceRequestStatus->name,
            'color' => $serviceRequestStatus->color->value,
        ])
        ->fillForm(EditServiceRequestStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestStatus::class, $serviceRequestStatus->toArray());
})->with(
    [
        'name missing' => [EditServiceRequestStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditServiceRequestStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [EditServiceRequestStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [EditServiceRequestStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('EditServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request_status.view-any');
    $user->givePermissionTo('service_request_status.*.update');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestStatusRequestFactory::new()->create());

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestStatus->fresh()->name);
});

test('EditServiceRequestStatus is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    $user->givePermissionTo('service_request_status.view-any');
    $user->givePermissionTo('service_request_status.*.update');

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestStatusRequestFactory::new()->create());

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestStatus->fresh()->name);
});
