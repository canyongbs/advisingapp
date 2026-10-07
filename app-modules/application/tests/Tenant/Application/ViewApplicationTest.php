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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\EditApplication;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationNotifications;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationSubmissions;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationWorkflows;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ViewApplication;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use Livewire\Livewire;
use App\Models\User;
use App\Settings\LicenseSettings;
use Livewire\Mechanisms\ComponentRegistry;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;
use function Tests\asSuperAdmin;

it('redirects an archived version edit URL to the active version', function () {
    seed(ApplicationSubmissionStateSeeder::class);
    asSuperAdmin();

    $application = Application::factory()->create(['archived_at' => now()]);
    $latestVersion = Application::factory()->create(['root_id' => $application->root_id]);
    $editUrl = ApplicationResource::getUrl('view', ['record' => $application, 'tab' => 'edit']);
    $latestEditUrl = ApplicationResource::getUrl('view', ['record' => $latestVersion, 'tab' => 'edit']);

    get($editUrl)->assertRedirect($latestEditUrl);
    get($latestEditUrl)->assertSuccessful();
});

it('does not resolve an archived application without an active version', function () {
    seed(ApplicationSubmissionStateSeeder::class);
    asSuperAdmin();

    $application = Application::factory()->create(['archived_at' => now()]);

    get(ApplicationResource::getUrl('view', ['record' => $application, 'tab' => 'edit']))
        ->assertNotFound();
});

it('archive action is always visible and labeled Archive', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $applicationWithSubmissions = Application::factory()->create();

    ApplicationSubmission::factory()->create([
        'application_id' => $applicationWithSubmissions->id,
    ]);

    $applicationWithoutSubmissions = Application::factory()->create();
    $applicationWithoutSubmissions->submissions()->delete();

    Livewire::test(ViewApplication::class, ['record' => $applicationWithSubmissions->getRouteKey()])
        ->assertActionVisible('archive')
        ->assertActionHasLabel('archive', 'Archive');

    Livewire::test(ViewApplication::class, ['record' => $applicationWithoutSubmissions->getRouteKey()])
        ->assertActionVisible('archive')
        ->assertActionHasLabel('archive', 'Archive');
});

it('archive action archives the application and redirects to the index when the application has submissions', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $application = Application::factory()->create();

    ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
    ]);

    Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
        ->callAction('archive')
        ->assertRedirect(ApplicationResource::getUrl('index'));

    expect($application->fresh()->isArchived())->toBeTrue();
});

it('exposes the mapped block types to the read-only fields rich editor for the custom block badges', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $application = Application::factory()->create();

    Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
        ->assertSeeHtml('data-mapped-block-types="' . implode(',', FormFieldBlockRegistry::getMappedBlockTypes()) . '"');
});

describe('authorization', function () {
    it('does not expose former View or Edit actions to management-only users', function () {
        seed(ApplicationSubmissionStateSeeder::class);
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.delete');
        actingAs($user);
        $application = Application::factory()->create();

        livewire(ViewApplication::class, ['record' => $application->getKey()])
            ->assertSet('activeTab', 'workflows')
            ->assertActionHidden('preview')
            ->assertActionHidden('archive')
            ->call('mountAction', 'archive')
            ->call('callMountedAction');

        expect($application->fresh()->isArchived())->toBeFalse();
    });
    it('falls back from an unavailable tab without rendering editable components', function (string $tab) {
        seed(ApplicationSubmissionStateSeeder::class);
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.view');
        actingAs($user);
        $application = Application::factory()->create();

        livewire(ViewApplication::class, ['record' => $application->getKey()])
            ->set('activeTab', $tab)
            ->assertSet('activeTab', 'view')
            ->assertDontSeeLivewire(EditApplication::class)
            ->assertDontSeeLivewire(ManageApplicationNotifications::class);

        get(ApplicationResource::getUrl('view', ['record' => $application, 'tab' => $tab]))->assertSuccessful();
    })->with(['edit', 'notifications', 'unknown']);

    it('renders only the active manager tab for a view-only user', function (string $tab, string $manager) {
        seed(ApplicationSubmissionStateSeeder::class);
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.view');
        actingAs($user);
        $application = Application::factory()->create();

        livewire(ViewApplication::class, ['record' => $application->getKey()])
            ->assertDontSee(app(ComponentRegistry::class)->getName(ManageApplicationSubmissions::class), stripInitialData: false)
            ->assertDontSee(app(ComponentRegistry::class)->getName(ManageApplicationWorkflows::class), stripInitialData: false)
            ->set('activeTab', $tab)
            ->assertSee(app(ComponentRegistry::class)->getName($manager), stripInitialData: false)
            ->assertDontSee(app(ComponentRegistry::class)->getName(EditApplication::class), stripInitialData: false)
            ->assertSet('activeTab', $tab);
    })->with([
        ['submissions', ManageApplicationSubmissions::class],
        ['workflows', ManageApplicationWorkflows::class],
    ]);

    it('allows update-only users to open the editor without exposing the View tab actions', function () {
        seed(ApplicationSubmissionStateSeeder::class);
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.update');
        actingAs($user);
        $application = Application::factory()->create();

        livewire(ViewApplication::class, ['record' => $application->getKey()])
            ->assertSet('activeTab', 'edit')
            ->assertSeeLivewire(EditApplication::class)
            ->assertActionHidden('preview')
            ->assertActionHidden('view');
    });

    it('denies the canonical page when no old page is accessible', function () {
        seed(ApplicationSubmissionStateSeeder::class);
        actingAs(User::factory()->licensed(LicenseType::cases())->create());
        $application = Application::factory()->create();

        get(ApplicationResource::getUrl('view', ['record' => $application]))->assertForbidden();
    });

    it('denies all tabs when the admissions feature is unavailable', function (string $tab) {
        seed(ApplicationSubmissionStateSeeder::class);
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.view', 'application.*.update');
        actingAs($user);
        $application = Application::factory()->create();
        $settings = app(LicenseSettings::class);
        $settings->data->addons->onlineAdmissions = false;
        $settings->save();

        get(ApplicationResource::getUrl('view', ['record' => $application, 'tab' => $tab]))->assertForbidden();
    })->with(['view', 'edit', 'workflows', 'submissions', 'notifications']);

    it('denies the canonical page without a required license', function () {
        seed(ApplicationSubmissionStateSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo('application.view-any', 'application.*.view', 'application.*.update');
        actingAs($user);
        $application = Application::factory()->create();

        get(ApplicationResource::getUrl('view', ['record' => $application]))->assertForbidden();
    });

    it('denies access to the active version before redirecting an archived URL', function () {
        seed(ApplicationSubmissionStateSeeder::class);
        $application = Application::factory()->create(['archived_at' => now()]);
        Application::factory()->create(['root_id' => $application->root_id]);
        $user = User::factory()->licensed(LicenseType::cases())->create();
        actingAs($user);

        get(ApplicationResource::getUrl('view', ['record' => $application, 'tab' => 'edit']))
            ->assertForbidden();
    });
});
