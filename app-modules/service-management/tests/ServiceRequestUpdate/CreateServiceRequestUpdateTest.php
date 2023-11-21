<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Support\Facades\Event;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\Notifications\Events\TriggeredAutoSubscription;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;
use Assist\ServiceManagement\Tests\RequestFactories\CreateServiceRequestUpdateRequestFactory;

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

    livewire(ServiceRequestUpdateResource\Pages\CreateServiceRequestUpdate::class)
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

    livewire(ServiceRequestUpdateResource\Pages\CreateServiceRequestUpdate::class)
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

    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('create')
        )->assertForbidden();

    livewire(ServiceRequestUpdateResource\Pages\CreateServiceRequestUpdate::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.create');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestUpdateRequestFactory::new()->create());

    livewire(ServiceRequestUpdateResource\Pages\CreateServiceRequestUpdate::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestUpdate::all());

    assertDatabaseHas(ServiceRequestUpdate::class, $request->toArray());
});
