<?php

use AdvisingApp\Organization\Filament\Resources\Organizations\OrganizationResource;
use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ListOrganizations;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use App\Features\OrganizationFeature;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

it('opens the `Edit` action in a slide-over', function (): void {
    $organization = Organization::factory()->create();

    Livewire::test(ListOrganizations::class)
        ->assertActionExists(
            TestAction::make(EditAction::class)->table($organization),
            fn (EditAction $action): bool => $action->isModalSlideOver()
        );
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

    it('hides the `Edit` action without the `organization.*.update` permission', function (): void {
        $user = User::factory()->licensed(Prospect::getLicenseType())->create();
        $user->givePermissionTo('organization.view-any');

        actingAs($user);

        $organization = Organization::factory()->create();

        Livewire::test(ListOrganizations::class)
            ->assertActionHidden(TestAction::make(EditAction::class)->table($organization));
    });
});
