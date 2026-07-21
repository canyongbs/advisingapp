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
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ListApplications;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Builder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function listApplicationsTestUser(): User
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineAdmissions = true;
    $settings->save();

    return User::factory()->licensed(LicenseType::cases())->create();
}

it('the duplicate action is gated by the create permission', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    $user = listApplicationsTestUser();
    $user->givePermissionTo('application.view-any');

    actingAs($user);

    $application = Application::factory()->create();

    livewire(ListApplications::class)
        ->assertTableActionHidden('Duplicate', $application);

    $user->givePermissionTo('application.create');

    livewire(ListApplications::class)
        ->assertTableActionVisible('Duplicate', $application);
});

// TODO: Write ListApplications tests
//test('The correct details are displayed on the ListApplications page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListApplications is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ApplicationResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('application.view-any');

    actingAs($user)
        ->get(
            ApplicationResource::getUrl('index')
        )->assertSuccessful();
});

test('ListApplications is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('application.view-any');

    actingAs($user)
        ->get(
            ApplicationResource::getUrl('index')
        )->assertForbidden();

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    actingAs($user)
        ->get(
            ApplicationResource::getUrl('index')
        )->assertSuccessful();
});

test('submissions count displays the correct count for an application', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $application = Application::factory()->create();

    $expectedCount = $application->submissions()->count();

    expect($expectedCount)->toBeGreaterThan(0);

    livewire(ListApplications::class)
        ->assertTableColumnStateSet('submissions_count', $expectedCount, $application);
});

test('submissions count includes submissions across all versions', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $application = Application::factory()->create();

    $existingCount = $application->submissions()->count();

    $archivedVersion = Application::factory()->create([
        'root_id' => $application->root_id,
        'archived_at' => now(),
    ]);

    $archivedCount = $archivedVersion->submissions()->count();

    livewire(ListApplications::class)
        ->assertTableColumnStateSet('submissions_count', $existingCount + $archivedCount, $application);
});

test('submissions count does not include submissions from unrelated applications', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $application = Application::factory()->create();

    $expectedCount = $application->submissions()->count();

    $unrelatedApplication = Application::factory()->create();

    livewire(ListApplications::class)
        ->assertTableColumnStateSet('submissions_count', $expectedCount, $application);
});
it('archives applications with submissions and deletes applications without submissions via the archive or delete bulk action', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $applicationWithSubmissions = Application::factory()->create();

    $applicationWithoutSubmissions = Application::factory()->create();
    $applicationWithoutSubmissions->submissions()->delete();

    $records = collect([$applicationWithSubmissions, $applicationWithoutSubmissions]);

    livewire(ListApplications::class)
        ->selectTableRecords($records->pluck('id')->all())
        ->callAction(TestAction::make('archive')->table()->bulk())
        ->assertNotified();

    expect($applicationWithSubmissions->fresh()->archived_at)->not->toBeNull();
    expect(Application::find($applicationWithoutSubmissions->id))->toBeNull();
});

it('marks an application as used when any version has submitted forms', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    $application = Application::factory()->create();
    $application->submissions()->delete();

    Application::factory()->create([
        'root_id' => $application->root_id,
        'archived_at' => now(),
    ]);

    expect($application->isUsed())->toBeTrue();
});

it('marks an application as unused when no version has submitted forms', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    $application = Application::factory()->create();
    $application->submissions()->delete();

    $archivedVersion = Application::factory()->create([
        'root_id' => $application->root_id,
        'archived_at' => now(),
    ]);
    $archivedVersion->submissions()->delete();

    expect($application->isUsed())->toBeFalse();
});

it('used query includes application roots that have submissions on any version', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    $application = Application::factory()->create();
    $application->submissions()->delete();

    Application::factory()->create([
        'root_id' => $application->root_id,
        'archived_at' => now(),
    ]);

    $unusedApplication = Application::factory()->create();
    $unusedApplication->submissions()->delete();

    $unusedArchivedVersion = Application::factory()->create([
        'root_id' => $unusedApplication->root_id,
        'archived_at' => now(),
    ]);
    $unusedArchivedVersion->submissions()->delete();

    $usedRootIds = Application::query()
        ->tap(fn (Builder $query) => $application->used($query))
        ->pluck('root_id')
        ->unique();

    expect($usedRootIds)->toContain($application->root_id)
        ->not->toContain($unusedApplication->root_id);
});
