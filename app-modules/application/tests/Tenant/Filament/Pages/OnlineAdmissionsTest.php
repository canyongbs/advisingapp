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

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Enums\OnlineAdmissionsTab;
use AdvisingApp\Application\Filament\Pages\OnlineAdmissions;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStates\ApplicationSubmissionStateResource;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

$enableOnlineAdmissions = function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineAdmissions = true;
    $settings->save();
};

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

it('can render the online admissions page', function (): void {
    asSuperAdmin();

    get(OnlineAdmissions::getUrl())
        ->assertSuccessful();
});

it('lists submission states', function (): void {
    asSuperAdmin();

    $states = ApplicationSubmissionState::factory()->count(3)->create();

    Livewire::test(OnlineAdmissions::class)
        ->assertCanSeeTableRecords($states);
});

it('does not list archived states with no submissions', function (): void {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    // @phpstan-ignore method.notFound
    $state->archive();

    Livewire::test(OnlineAdmissions::class)
        ->assertCanNotSeeTableRecords([$state]);
});

it('defaults to the first visible tab when the requested tab is unknown', function (): void {
    asSuperAdmin();

    Livewire::withQueryParams(['tab' => 'does-not-exist'])
        ->test(OnlineAdmissions::class)
        ->assertSet('activeTab', OnlineAdmissionsTab::SubmissionStates->value);
});

describe('actions', function (): void {
    it('links the create action to the resource create page', function (): void {
        asSuperAdmin();

        Livewire::test(OnlineAdmissions::class)
            ->assertActionHasUrl(
                TestAction::make('create')->table(),
                ApplicationSubmissionStateResource::getUrl('create'),
            );
    });

    it('links the view and edit actions to the resource pages', function (): void {
        asSuperAdmin();

        $state = ApplicationSubmissionState::factory()->create();

        Livewire::test(OnlineAdmissions::class)
            ->assertActionHasUrl(
                TestAction::make('view')->table($state),
                ApplicationSubmissionStateResource::getUrl('view', ['record' => $state]),
            )
            ->assertActionHasUrl(
                TestAction::make('edit')->table($state),
                ApplicationSubmissionStateResource::getUrl('edit', ['record' => $state]),
            );
    });
});

describe('old cluster URLs', function (): void {
    $oldSubmissionStatesUrl = fn (): string => OnlineAdmissions::getUrl() . '/application-submission-states';

    it('redirects the old list URL to the submission states tab', function () use ($oldSubmissionStatesUrl): void {
        asSuperAdmin();

        get($oldSubmissionStatesUrl())
            ->assertRedirect(OnlineAdmissions::getUrl(['tab' => OnlineAdmissionsTab::SubmissionStates->value]));
    });

    it('redirects the old create URL to the resource create page', function () use ($oldSubmissionStatesUrl): void {
        asSuperAdmin();

        get("{$oldSubmissionStatesUrl()}/create")
            ->assertRedirect(ApplicationSubmissionStateResource::getUrl('create'));
    });

    it('redirects the old view URL to the resource view page', function () use ($oldSubmissionStatesUrl): void {
        asSuperAdmin();

        $state = ApplicationSubmissionState::factory()->create();

        get("{$oldSubmissionStatesUrl()}/{$state->getRouteKey()}")
            ->assertRedirect(ApplicationSubmissionStateResource::getUrl('view', ['record' => $state]));
    });

    it('redirects the old edit URL to the resource edit page', function () use ($oldSubmissionStatesUrl): void {
        asSuperAdmin();

        $state = ApplicationSubmissionState::factory()->create();

        get("{$oldSubmissionStatesUrl()}/{$state->getRouteKey()}/edit")
            ->assertRedirect(ApplicationSubmissionStateResource::getUrl('edit', ['record' => $state]));
    });
});

describe('authorization', function () use ($enableOnlineAdmissions, $getTabLabels): void {
    it('shows the `Submission States` tab to a user with the `settings.view-any` permission', function () use ($enableOnlineAdmissions, $getTabLabels): void {
        $enableOnlineAdmissions();

        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $component = Livewire::test(OnlineAdmissions::class);

        $component->assertSuccessful();

        expect($getTabLabels($component))->toContain('Submission States');
    });

    it('denies access to the page without the `settings.view-any` permission', function () use ($enableOnlineAdmissions): void {
        $enableOnlineAdmissions();

        $user = User::factory()->create();
        actingAs($user);

        get(OnlineAdmissions::getUrl())
            ->assertForbidden();
    });

    it('denies access to the page when the online admissions feature is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->onlineAdmissions = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        get(OnlineAdmissions::getUrl())
            ->assertForbidden();
    });

    it('hides the create, view, and edit actions from a view-only user', function () use ($enableOnlineAdmissions): void {
        $enableOnlineAdmissions();

        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $state = ApplicationSubmissionState::factory()->create();

        Livewire::test(OnlineAdmissions::class)
            ->assertCanSeeTableRecords([$state])
            ->assertTableActionHidden('create')
            ->assertTableActionHidden('view', $state)
            ->assertTableActionHidden('edit', $state);
    });

    it('shows the create, view, and edit actions to a user with the matching permissions', function () use ($enableOnlineAdmissions): void {
        $enableOnlineAdmissions();

        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any', 'settings.create', 'settings.*.view', 'settings.*.update');
        actingAs($user);

        $state = ApplicationSubmissionState::factory()->create();

        Livewire::test(OnlineAdmissions::class)
            ->assertTableActionVisible('create')
            ->assertTableActionVisible('view', $state)
            ->assertTableActionVisible('edit', $state);
    });
});
