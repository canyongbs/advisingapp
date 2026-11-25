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
        'phone_number' => '+15185551234',
        'website' => 'https://acme.test',
        'address' => '123 Main Street',
        'city' => 'Troy',
        'state' => 'NY',
        'postalcode' => '12180',
        'country' => 'United States',
    ]);

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => 'Acme Industries',
            'description' => 'Makers of everything you could ever need.',
            'email' => 'hello@acme.test',
            'phone_number' => '+15185551234',
            'website' => 'https://acme.test',
            'address' => '123 Main Street',
            'city' => 'Troy',
            'state' => 'NY',
            'postalcode' => '12180',
            'country' => 'United States',
        ], 'infolist');
});

it('displays the organization domains', function (): void {
    $organization = Organization::factory()->create(['domains' => [['domain' => 'acme.com'], ['domain' => 'acme.org']]]);

    Livewire::test(ViewOrganization::class, ['record' => $organization->getRouteKey()])
        ->assertSchemaComponentStateSet('domains', ['acme.com', 'acme.org'], 'infolist');
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
