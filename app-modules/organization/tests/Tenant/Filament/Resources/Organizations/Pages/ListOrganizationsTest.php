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
use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ListOrganizations;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use App\Features\OrganizationFeature;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    asSuperAdmin();

    // The flag has no activation migration yet, so it is inactive in tests.
    OrganizationFeature::activate();
});

it('can list non soft deleted organizations', function (): void {
    $organizations = Organization::factory()->count(2)->create();
    $deletedOrganization = Organization::factory()->create(['deleted_at' => now()]);

    Livewire::test(ListOrganizations::class)
        ->assertCanSeeTableRecords($organizations)
        ->assertCanNotSeeTableRecords([$deletedOrganization]);
});

it('can search organizations by name', function (): void {
    $matchingOrganization = Organization::factory()->create(['name' => 'Acme Inc']);
    $otherOrganization = Organization::factory()->create(['name' => 'Globex Corporation']);

    Livewire::test(ListOrganizations::class)
        ->searchTable('Acme')
        ->assertCanSeeTableRecords([$matchingOrganization])
        ->assertCanNotSeeTableRecords([$otherOrganization]);
});

it('shows the description under the name, truncated after 50 characters', function (): void {
    $organization = Organization::factory()->create(['description' => str_repeat('a', 60)]);

    Livewire::test(ListOrganizations::class)
        ->assertTableColumnHasDescription('name', str_repeat('a', 50) . '...', $organization);
});

it('shows the address details under the address', function (array $attributes, ?HtmlString $description): void {
    $organization = Organization::factory()->create($attributes);

    Livewire::test(ListOrganizations::class)
        ->assertTableColumnHasDescription('address', $description, $organization);
})->with([
    'all details' => [
        ['city' => 'Troy', 'state' => 'NY', 'postalcode' => '12180', 'country' => 'United States'],
        new HtmlString('Troy, NY<br>12180 United States'),
    ],
    'some details blank' => [
        ['city' => 'Troy', 'state' => null, 'postalcode' => null, 'country' => 'United States'],
        new HtmlString('Troy<br>United States'),
    ],
    'no details' => [
        ['city' => null, 'state' => null, 'postalcode' => null, 'country' => null],
        null,
    ],
]);

it('opens the `New` action in a slide-over', function (): void {
    Livewire::test(ListOrganizations::class)
        ->assertActionExists(CreateAction::class, fn (CreateAction $action): bool => $action->isModalSlideOver());
});

it('links each row to the organization\'s view page', function (): void {
    $organization = Organization::factory()->create();

    $page = Livewire::test(ListOrganizations::class)->instance();

    assert($page instanceof ListOrganizations);

    expect($page->getTable()->getRecordUrl($organization))
        ->toBe(OrganizationResource::getUrl('view', ['record' => $organization]));
});

it('clears the organization from its prospects when bulk deleted', function (): void {
    $organizations = Organization::factory()->count(2)->create();
    $prospects = $organizations->map(
        fn (Organization $organization): Prospect => Prospect::factory()->for($organization)->create()
    );

    expect($prospects->every(fn (Prospect $prospect): bool => filled($prospect->organization_id)))->toBeTrue();

    Livewire::test(ListOrganizations::class)
        ->selectTableRecords($organizations)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk());

    $organizations->each(fn (Organization $organization) => assertSoftDeleted($organization));

    expect($prospects->every(fn (Prospect $prospect): bool => blank($prospect->refresh()->organization_id)))->toBeTrue();
});

describe('authorization', function (): void {
    it('denies access to the list page without the `organization.view-any` permission', function (): void {
        actingAs(User::factory()->licensed(Prospect::getLicenseType())->create())
            ->get(OrganizationResource::getUrl('index'))
            ->assertForbidden();
    });

    it('allows access to the list page with the `organization.view-any` permission', function (): void {
        $user = User::factory()->licensed(Prospect::getLicenseType())->create();
        $user->givePermissionTo('organization.view-any');

        actingAs($user)
            ->get(OrganizationResource::getUrl('index'))
            ->assertSuccessful();
    });

    it('denies access to the list page without the Recruitment CRM license', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('organization.view-any');

        actingAs($user)
            ->get(OrganizationResource::getUrl('index'))
            ->assertForbidden();
    });

    it('hides the `New` action without the `organization.create` permission', function (): void {
        $user = User::factory()->licensed(Prospect::getLicenseType())->create();
        $user->givePermissionTo('organization.view-any');

        actingAs($user);

        Livewire::test(ListOrganizations::class)
            ->assertActionHidden(CreateAction::class);
    });
});
