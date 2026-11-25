<?php

use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ListOrganizations;
use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ViewOrganization;
use AdvisingApp\Organization\Models\Organization;
use App\DataTransferObjects\AutocompletedAddress;
use App\Features\OrganizationFeature;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Exceptions;
use Livewire\Livewire;
use Spatie\LaravelData\Exceptions\CannotCreateData;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
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
