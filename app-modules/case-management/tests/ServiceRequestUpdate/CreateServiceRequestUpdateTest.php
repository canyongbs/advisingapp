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

use Illuminate\Support\Facades\Event;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Models\ServiceRequestUpdate;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\CaseManagement\Filament\Resources\ServiceRequestUpdateResource;
use AdvisingApp\CaseManagement\Tests\RequestFactories\CreateServiceRequestUpdateRequestFactory;
use AdvisingApp\CaseManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\CreateServiceRequestUpdate;

test('A successful action on the CreateServiceRequestUpdate page', function () {
    // Because we create a ServiceRequest there is already a Subscription created.
    // This causes an issue during SubscriptionCreate as a unique constraint is violated.
    // Postgres prevents any further actions from happening during a transaction when there is an error like this
    // Preventing the Subscription creation for now
    Event::fake([TriggeredAutoSubscription::class]);

    asSuperAdmin()
        ->get(
            ServiceRequestUpdateResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateServiceRequestUpdateRequestFactory::new()->create());

    livewire(CreateServiceRequestUpdate::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestUpdate::all());

    assertDatabaseHas(ServiceRequestUpdate::class, $request->except('service_request_id')->toArray());

    expect(ServiceRequestUpdate::first()->serviceRequest->id)
        ->toEqual($request->get('service_request_id'));
});

test('CreateServiceRequestUpdate requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CreateServiceRequestUpdate::class)
        ->fillForm(CreateServiceRequestUpdateRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestUpdate::all());
})->with(
    [
        'service_request missing' => [CreateServiceRequestUpdateRequestFactory::new()->without('service_request_id'), ['service_request_id' => 'required']],
        'service_request not existing service_request id' => [CreateServiceRequestUpdateRequestFactory::new()->state(['service_request_id' => fake()->uuid()]), ['service_request_id' => 'exists']],
        'update missing' => [CreateServiceRequestUpdateRequestFactory::new()->without('update'), ['update' => 'required']],
        'update is not a string' => [CreateServiceRequestUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'direction missing' => [CreateServiceRequestUpdateRequestFactory::new()->state(['direction' => null]), ['direction' => 'required']],
        'internal not a boolean' => [CreateServiceRequestUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);

// Permission Tests

test('CreateServiceRequestUpdate is gated with proper access control', function () {
    // Because we create a ServiceRequest there is already a Subscription created.
    // This causes an issue during SubscriptionCreate as a unique constraint is violated.
    // Postgres prevents any further actions from happening during a transaction when there is an error like this
    // Preventing the Subscription creation for now
    Event::fake([TriggeredAutoSubscription::class]);

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateServiceRequestUpdate::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.create');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestUpdateRequestFactory::new()->create());

    livewire(CreateServiceRequestUpdate::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestUpdate::all());

    assertDatabaseHas(ServiceRequestUpdate::class, $request->toArray());
});

test('CreateServiceRequestUpdate is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.create');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateServiceRequestUpdate::class)
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestUpdateRequestFactory::new()->create());

    livewire(CreateServiceRequestUpdate::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestUpdate::all());

    assertDatabaseHas(ServiceRequestUpdate::class, $request->toArray());
});
