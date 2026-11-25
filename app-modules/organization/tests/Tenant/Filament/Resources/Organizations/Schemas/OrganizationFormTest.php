<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ListOrganizations;
use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ViewOrganization;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Organization\Rules\UniqueOrganizationDomain;
use App\DataTransferObjects\AutocompletedAddress;
use App\Features\OrganizationFeature;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Exceptions;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Spatie\LaravelData\Exceptions\CannotCreateData;

use function Tests\asSuperAdmin;

beforeEach(function (): void {
    asSuperAdmin();

    // The flag has no activation migration yet, so it is inactive in tests.
    OrganizationFeature::activate();
});

it('can create an organization', function (): void {
    $data = Arr::except(Organization::factory()->raw(), 'domains');

    assertDatabaseMissing(Organization::class, ['name' => $data['name']]);

    Livewire::test(ListOrganizations::class)
        ->callAction(CreateAction::class, $data)
        ->assertHasNoFormErrors();

    assertDatabaseHas(Organization::class, [
        'name' => $data['name'],
        'email' => $data['email'],
        'website' => $data['website'],
        'city' => $data['city'],
    ]);
});

it('can create an organization with domains', function (): void {
    $repeaterFake = Repeater::fake();

    $data = Organization::factory()->raw();

    Livewire::test(ListOrganizations::class)
        ->callAction(CreateAction::class, $data)
        ->assertHasNoFormErrors();

    expect(Organization::query()->where('name', $data['name'])->sole()->domains)->toBe($data['domains']);

    $repeaterFake();
});

it('saves domains in lowercase', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $data = [...Organization::factory()->raw(), 'domains' => [['domain' => 'ACME.com']]];

    Livewire::test(ListOrganizations::class)
        ->callAction(CreateAction::class, $data)
        ->assertHasNoFormErrors();

    expect(Organization::query()->where('name', $data['name'])->sole()->domains)->toBe([['domain' => 'acme.com']]);

    $undoRepeaterFake();
});

it('can update an organization', function (): void {
    $organization = Organization::factory()->create(['name' => 'Acme Industries']);

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->callAction(EditAction::class, ['name' => 'Acme Holdings'])
        ->assertHasNoFormErrors();

    expect($organization->refresh()->name)->toBe('Acme Holdings');
});

it('allows an organization to keep its own name when updated', function (): void {
    $organization = Organization::factory()->create(['name' => 'Acme Industries']);

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->callAction(EditAction::class, ['name' => 'Acme Industries', 'email' => 'hello@acme.test'])
        ->assertHasNoFormErrors();

    expect($organization->refresh()->email)->toBe('hello@acme.test');
});

it('allows an organization to keep its own domains when updated', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.com']]]);

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->callAction(EditAction::class, ['email' => 'hello@acme.test'])
        ->assertHasNoFormErrors();

    expect($organization->refresh())
        ->email->toBe('hello@acme.test')
        ->domains->toBe([['domain' => 'acme.com']]);

    $undoRepeaterFake();
});

it('fills the address fields when an address suggestion is selected', function (): void {
    Livewire::test(ListOrganizations::class)
        ->mountAction(CreateAction::class)
        ->assertSchemaStateSet([
            'address' => null,
            'city' => null,
            'state' => null,
            'postalcode' => null,
            'country' => null,
        ])
        ->call('callSchemaComponentMethod', 'mountedActionSchema0.address', 'reactOnItemSelectedFromJs', [
            'item' => [
                'value' => '1800 Highland Ave, Troy, NY, 12180, US',
                'label' => '1800 Highland Ave, Troy, NY, 12180, US',
                'data' => [
                    'data' => new AutocompletedAddress(
                        line1: '1800 Highland Ave',
                        city: 'Troy',
                        state: 'NY',
                        postalCode: '12180',
                        country: 'US',
                        label: '1800 Highland Ave, Troy, NY, 12180, US',
                    ),
                ],
            ],
        ])
        ->assertSchemaStateSet([
            'address' => '1800 Highland Ave',
            'city' => 'Troy',
            'state' => 'NY',
            'postalcode' => '12180',
            'country' => 'US',
        ]);
});

it('notifies the user and reports the error when a selected address suggestion cannot be read', function (): void {
    Exceptions::fake();

    Livewire::test(ListOrganizations::class)
        ->mountAction(CreateAction::class)
        ->call('callSchemaComponentMethod', 'mountedActionSchema0.address', 'reactOnItemSelectedFromJs', [
            'item' => [
                'value' => '1800 Highland Ave',
                'label' => '1800 Highland Ave',
                'data' => [
                    'data' => ['line1' => '1800 Highland Ave'],
                ],
            ],
        ])
        ->assertNotified('Failed to fetch address suggestions')
        ->assertSchemaStateSet([
            'city' => null,
            'state' => null,
            'postalcode' => null,
            'country' => null,
        ]);

    Exceptions::assertReported(CannotCreateData::class);
});

it('validates the inputs', function (array $data, array $errors): void {
    Livewire::test(ListOrganizations::class)
        ->callAction(CreateAction::class, [...Arr::except(Organization::factory()->raw(), 'domains'), ...$data])
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [['name' => null], ['name' => 'required']],
    'name max' => [['name' => str_repeat('a', 256)], ['name' => 'max']],
    'name unique' => function (): array {
        Organization::factory()->create(['name' => 'ACME Inc']);

        return [['name' => 'acme inc'], ['name' => 'unique']];
    },
    'email email' => [['email' => 'not-an-email'], ['email' => 'email']],
    'website url' => [['website' => 'not a url'], ['website' => 'url']],
]);

it('validates the domains', function (array $domains, array $errors): void {
    $undoRepeaterFake = Repeater::fake();

    Livewire::test(ListOrganizations::class)
        ->callAction(CreateAction::class, [...Organization::factory()->raw(), 'domains' => $domains])
        ->assertHasFormErrors($errors);

    $undoRepeaterFake();
})->with([
    'domain required' => [[['domain' => null]], ['domains.0.domain' => 'required']],
    'domain max' => [[['domain' => str_repeat('a', 252) . '.com']], ['domains.0.domain' => 'max']],
    'domain format' => [[['domain' => 'not a domain']], ['domains.0.domain' => 'regex']],
    'domain distinct' => [
        [['domain' => 'acme.com'], ['domain' => 'ACME.com']],
        ['domains.1.domain' => 'This domain is already in use and may not be used a second time.'],
    ],
    'domain unique' => function (): array {
        Organization::factory()->create(['domains' => [['domain' => 'acme.com']]]);

        return [[['domain' => 'acme.com']], ['domains.0.domain' => UniqueOrganizationDomain::class]];
    },
]);
