<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
      same in return. Canyon GBS® and Advising App® are registered trademarks of
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

use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\CreateProspect;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectAddress;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\Prospect\Tests\Tenant\Prospect\RequestFactories\CreateProspectRequestFactory;
use App\DataTransferObjects\AutocompletedAddress;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Arr;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

// TODO: Write CreateProspect page tests
//test('A successful action on the CreateProspect page', function () {});
//
//test('CreateProspect requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateProspect is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateProspect::class)
        ->assertForbidden();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');

    actingAs($user)
        ->get(
            ProspectResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateProspectRequestFactory::new()->create([
        'created_by_id' => $user->id,
    ]));

    $undoRepeaterFake = Repeater::fake();

    livewire(CreateProspect::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    assertCount(1, Prospect::all());

    /** @var array<array<mixed>> $emailAddresses */
    $emailAddresses = $request->toArray()['emailAddresses'];

    /** @var array<array<mixed>> $phoneNumbers */
    $phoneNumbers = $request->toArray()['phoneNumbers'];

    /** @var array<array<mixed>> $addresses */
    $addresses = $request->toArray()['addresses'];

    assertDatabaseHas(Prospect::class, Arr::except($request->toArray(), ['emailAddresses', 'phoneNumbers', 'addresses']));
    assertDatabaseHas(ProspectEmailAddress::class, Arr::first($emailAddresses));
    assertDatabaseHas(ProspectPhoneNumber::class, Arr::first($phoneNumbers));
    assertDatabaseHas(ProspectAddress::class, Arr::first($addresses));
});

it('selecting an address in the AddressInput sets the address fields', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');
    actingAs($user);

    $component = livewire(CreateProspect::class);

    $addresses = $component->get('data.addresses');
    $itemUuid = array_key_first($addresses);

    $component
        ->call('callSchemaComponentMethod', "form.addresses.{$itemUuid}.address", 'reactOnItemSelectedFromJs', [
            'item' => [
                'value' => '456 Oak Ave, Springfield, IL, 62701, US',
                'label' => '456 Oak Ave, Springfield, IL, 62701, US',
                'data' => [
                    'data' => new AutocompletedAddress(
                        line1: '456 Oak Ave',
                        city: 'Springfield',
                        state: 'IL',
                        postalCode: '62701',
                        country: 'US',
                        label: '456 Oak Ave, Springfield, IL, 62701, US',
                    ),
                ],
            ],
        ])
        ->assertFormSet([
            "addresses.{$itemUuid}.line_1" => '456 Oak Ave',
            "addresses.{$itemUuid}.city" => 'Springfield',
            "addresses.{$itemUuid}.state" => 'IL',
            "addresses.{$itemUuid}.postal" => '62701',
            "addresses.{$itemUuid}.country" => 'US',
        ]);
});

it('can create a prospect with an address', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');
    actingAs($user);

    $uuid = (string) str()->uuid();
    $undoRepeaterFake = Repeater::fake();

    livewire(CreateProspect::class)
        ->fillForm([
            'status_id' => ProspectStatus::factory()->create()->id,
            'source_id' => ProspectSource::factory()->create()->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'full_name' => 'Jane Smith',
            'created_by_id' => $user->id,
            'addresses' => [
                $uuid => [
                    'line_1' => '789 Elm St',
                    'city' => 'Austin',
                    'state' => 'TX',
                    'postal' => '78701',
                    'country' => 'US',
                    'type' => 'Home',
                ],
            ],
            'emailAddresses' => [
                $uuid => [
                    'address' => 'jane.smith@test.com',
                ],
            ],
            'phoneNumbers' => [
                $uuid => [
                    'number' => '+15555559876',
                    'type' => 'Mobile',
                    'can_receive_sms' => true,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $prospect = Prospect::where('first_name', 'Jane')->where('last_name', 'Smith')->first();
    expect($prospect)->not->toBeNull();
    expect($prospect->first_name)->toBe('Jane');
    expect($prospect->last_name)->toBe('Smith');
    expect($prospect->addresses()->count())->toBe(1);

    $address = $prospect->addresses()->first();
    expect($address->line_1)->toBe('789 Elm St');
    expect($address->city)->toBe('Austin');
    expect($address->state)->toBe('TX');
    expect($address->postal)->toBe('78701');
    expect($address->country)->toBe('US');

    $undoRepeaterFake();
});
