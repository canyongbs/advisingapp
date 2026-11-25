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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\ResourceHub\Enums\ResourceHubTab;
use AdvisingApp\ResourceHub\Filament\Pages\ResourceHub;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubCategories\ResourceHubCategoryResource;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubQualities\ResourceHubQualityResource;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubStatuses\ResourceHubStatusResource;
use AdvisingApp\ResourceHub\Livewire\ResourceHubCategoriesTable;
use AdvisingApp\ResourceHub\Livewire\ResourceHubQualitiesTable;
use AdvisingApp\ResourceHub\Livewire\ResourceHubStatusesTable;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use AdvisingApp\ResourceHub\Models\ResourceHubStatus;
use App\Enums\NavigationGroup;
use App\Models\User;
use App\Settings\LicenseSettings;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

$getTabLabels = function (Testable $component): array {
    $document = new DOMDocument();
    $document->loadHTML($component->html(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $tabs = (new DOMXPath($document))->query('//*[@role="tab"]');
    assert($tabs !== false);

    $labels = [];

    foreach ($tabs as $tab) {
        $labels[] = trim((string) preg_replace('/\s+/', ' ', $tab->textContent));
    }

    return $labels;
};

it('renders the resource hub page', function (): void {
    asSuperAdmin();

    get(ResourceHub::getUrl())
        ->assertSuccessful();
});

it('keeps the navigation label, group and sort of the old cluster', function (): void {
    expect(ResourceHub::getNavigationLabel())->toBe('Resource Hub')
        ->and(ResourceHub::getNavigationSort())->toBe(110)
        ->and(ResourceHub::getNavigationGroup())->toBe(NavigationGroup::Settings);
});

it('includes the resource routes in the navigation active pattern', function (): void {
    expect(ResourceHub::getNavigationItemActiveRoutePattern())
        ->toBe([
            ResourceHub::getRouteName(),
            ResourceHubCategoryResource::getRouteBaseName() . '.*',
            ResourceHubQualityResource::getRouteBaseName() . '.*',
            ResourceHubStatusResource::getRouteBaseName() . '.*',
        ]);
});

it('shows the tabs in order', function () use ($getTabLabels): void {
    asSuperAdmin();

    $component = Livewire::test(ResourceHub::class);

    expect($getTabLabels($component))->toBe(['Categories', 'Qualities', 'Statuses']);
});

it('renders only the selected tab', function (ResourceHubTab $tab, string $visibleTable, array $hiddenTables): void {
    asSuperAdmin();

    $component = Livewire::withQueryParams(['tab' => $tab->value])
        ->test(ResourceHub::class)
        ->assertSet('activeTab', $tab->value)
        ->assertSeeLivewire($visibleTable);

    foreach ($hiddenTables as $hiddenTable) {
        $component->assertDontSeeLivewire($hiddenTable);
    }
})->with([
    'categories' => [ResourceHubTab::Categories, ResourceHubCategoriesTable::class, [ResourceHubQualitiesTable::class, ResourceHubStatusesTable::class]],
    'qualities' => [ResourceHubTab::Qualities, ResourceHubQualitiesTable::class, [ResourceHubCategoriesTable::class, ResourceHubStatusesTable::class]],
    'statuses' => [ResourceHubTab::Statuses, ResourceHubStatusesTable::class, [ResourceHubCategoriesTable::class, ResourceHubQualitiesTable::class]],
]);

it('defaults to the first visible tab when the requested tab is unknown', function (): void {
    asSuperAdmin();

    Livewire::withQueryParams(['tab' => 'does-not-exist'])
        ->test(ResourceHub::class)
        ->assertSet('activeTab', ResourceHubTab::Categories->value);
});

it('defaults to the first visible tab when no tab is requested', function (): void {
    asSuperAdmin();

    Livewire::test(ResourceHub::class)
        ->assertSet('activeTab', ResourceHubTab::Categories->value);
});

describe('authorization', function () use ($getTabLabels): void {
    it('shows every tab to a user with the `settings.view-any` permission', function () use ($getTabLabels): void {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $component = Livewire::test(ResourceHub::class);

        $component->assertSuccessful();

        expect($getTabLabels($component))->toBe(['Categories', 'Qualities', 'Statuses']);
    });

    it('denies access without the `settings.view-any` permission', function (): void {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        actingAs($user);

        get(ResourceHub::getUrl())
            ->assertForbidden();
    });

    it('denies access when the resource hub feature is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->resourceHub = false;
        $settings->save();

        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        get(ResourceHub::getUrl())
            ->assertForbidden();

        $settings->data->addons->resourceHub = true;
        $settings->save();

        get(ResourceHub::getUrl())
            ->assertSuccessful();
    });

    it('denies access without a recruitment or retention license', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        get(ResourceHub::getUrl())
            ->assertForbidden();

        $user->grantLicense(LicenseType::RecruitmentCrm);
        $user->refresh();

        get(ResourceHub::getUrl())
            ->assertSuccessful();
    });
});

describe('old cluster URLs', function (): void {
    $oldUrl = fn (string $segment): string => ResourceHub::getUrl() . "/{$segment}";

    it('redirects the old list URLs to their tab', function (string $segment, ResourceHubTab $tab) use ($oldUrl): void {
        asSuperAdmin();

        get($oldUrl($segment))
            ->assertRedirect(ResourceHub::getUrl(['tab' => $tab->value]));
    })->with([
        'categories' => ['resource-hub-categories', ResourceHubTab::Categories],
        'qualities' => ['resource-hub-qualities', ResourceHubTab::Qualities],
        'statuses' => ['resource-hub-statuses', ResourceHubTab::Statuses],
    ]);

    it('preserves query parameters on old list URL redirects', function () use ($oldUrl): void {
        asSuperAdmin();

        $response = get($oldUrl('resource-hub-categories') . '?search=abc')
            ->assertRedirect();

        parse_str(parse_url((string) $response->headers->get('Location'), PHP_URL_QUERY) ?? '', $query);

        expect($query)->toMatchArray(['tab' => ResourceHubTab::Categories->value, 'search' => 'abc']);
    });

    it('redirects the old create, view and edit URLs to the resource pages', function (string $segment, string $resource, string $model) use ($oldUrl): void {
        asSuperAdmin();

        $record = $model::factory()->create();

        get($oldUrl("{$segment}/create"))
            ->assertRedirect($resource::getUrl('create'));

        get($oldUrl("{$segment}/{$record->getRouteKey()}"))
            ->assertRedirect($resource::getUrl('view', ['record' => $record]));

        get($oldUrl("{$segment}/{$record->getRouteKey()}/edit"))
            ->assertRedirect($resource::getUrl('edit', ['record' => $record]));
    })->with([
        'categories' => ['resource-hub-categories', ResourceHubCategoryResource::class, ResourceHubCategory::class],
        'qualities' => ['resource-hub-qualities', ResourceHubQualityResource::class, ResourceHubQuality::class],
        'statuses' => ['resource-hub-statuses', ResourceHubStatusResource::class, ResourceHubStatus::class],
    ]);
});
