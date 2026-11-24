<?php

use AdvisingApp\Organization\Filament\Resources\Organizations\OrganizationResource;
use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ViewOrganization;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use App\Features\OrganizationFeature;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    asSuperAdmin();

    // The flag has no activation migration yet, so it is inactive in tests.
    OrganizationFeature::activate();
});

it('can render the view organization page', function (): void {
    $organization = Organization::factory()->create();

    get(OrganizationResource::getUrl('view', ['record' => $organization]))
        ->assertSuccessful();
});

it('displays the organization data', function (): void {
    $organization = Organization::factory()->create([
        'name' => 'Acme Industries',
        'description' => 'Makers of everything you could ever need.',
        'email' => 'hello@acme.test',
        'website' => 'https://acme.test',
        'address' => '123 Main Street',
        'city' => 'Troy',
        'state' => 'NY',
        'postalcode' => '12180',
        'country' => 'United States',
    ]);

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->assertSeeText([
            'Acme Industries',
            'Makers of everything you could ever need.',
            'hello@acme.test',
            'https://acme.test',
            '123 Main Street',
            'Troy',
            'NY',
            '12180',
            'United States',
        ]);
});

it('opens the `Edit` action in a slide-over', function (): void {
    $organization = Organization::factory()->create();

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->assertActionExists(EditAction::class, fn (EditAction $action): bool => $action->isModalSlideOver());
});

it('can delete an organization', function (): void {
    $organization = Organization::factory()->create();

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->callAction(DeleteAction::class)
        ->assertRedirect(OrganizationResource::getUrl('index'));

    assertSoftDeleted($organization);
});

it('clears the organization from its prospects when deleted', function (): void {
    $organization = Organization::factory()->create();
    $prospect = Prospect::factory()->for($organization)->create();

    expect($prospect->organization_id)->toBe($organization->getKey());

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect($prospect->refresh()->organization_id)->toBeNull();
});

describe('authorization', function (): void {
    it('denies access to the view page without the `organization.*.view` permission', function (): void {
        $user = User::factory()->licensed(Prospect::getLicenseType())->create();
        $user->givePermissionTo('organization.view-any');

        $organization = Organization::factory()->create();

        actingAs($user)
            ->get(OrganizationResource::getUrl('view', ['record' => $organization]))
            ->assertForbidden();
    });

    it('hides the `Edit` action without the `organization.*.update` permission', function (): void {
        $user = User::factory()->licensed(Prospect::getLicenseType())->create();
        $user->givePermissionTo(['organization.view-any', 'organization.*.view']);

        actingAs($user);

        $organization = Organization::factory()->create();

        Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
            ->assertActionHidden(EditAction::class);
    });

    it('hides the `Delete` action without the `organization.*.delete` permission', function (): void {
        $user = User::factory()->licensed(Prospect::getLicenseType())->create();
        $user->givePermissionTo(['organization.view-any', 'organization.*.view']);

        actingAs($user);

        $organization = Organization::factory()->create();

        Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);
    });
});
