<?php

use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ListOrganizations;
use AdvisingApp\Organization\Models\Organization;
use App\Features\OrganizationFeature;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Arr;
use Livewire\Livewire;

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

    Livewire::test(ListOrganizations::class)
        ->callAction(TestAction::make(EditAction::class)->table($organization), ['name' => 'Acme Holdings'])
        ->assertHasNoFormErrors();

    expect($organization->refresh()->name)->toBe('Acme Holdings');
});

it('allows an organization to keep its own name when updated', function (): void {
    $organization = Organization::factory()->create(['name' => 'Acme Industries']);

    Livewire::test(ListOrganizations::class)
        ->callAction(TestAction::make(EditAction::class)->table($organization), ['name' => 'Acme Industries', 'email' => 'hello@acme.test'])
        ->assertHasNoFormErrors();

    expect($organization->refresh()->email)->toBe('hello@acme.test');
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
