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
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

describe('duplication', function () {
    beforeEach(function () {
        seed(ApplicationSubmissionStateSeeder::class);

        asSuperAdmin();
    });

    it('can duplicate an application its steps and its fields', function () {
        $application = Application::factory()->create();

        expect(Application::count())->toBe(1);

        livewire(ListApplications::class)
            ->callAction(TestAction::make('Duplicate')->table($application))
            ->assertHasNoFormErrors();

        $duplicatedApplication = Application::query()->whereKeyNot($application->getKey())->firstOrFail();

        expect(Application::count())->toBe(2)
            ->and($duplicatedApplication->name)->toBe("Copy - {$application->name}")
            ->and($duplicatedApplication->fields()->count())->toBe($application->fields()->count())
            ->and($duplicatedApplication->steps()->count())->toBe($application->steps()->count());
    });

    it('does not duplicate application submissions', function () {
        $application = Application::factory()->create();

        $submissionCount = ApplicationSubmission::count();

        expect($submissionCount)->toBeGreaterThan(0);

        livewire(ListApplications::class)
            ->callAction(TestAction::make('Duplicate')->table($application))
            ->assertHasNoFormErrors();

        $duplicatedApplication = Application::query()->whereKeyNot($application->getKey())->firstOrFail();

        expect(ApplicationSubmission::count())->toBe($submissionCount)
            ->and($duplicatedApplication->submissions()->count())->toBe(0);
    });

    it('gives a duplicated application its own version tree rather than sharing the original', function () {
        $application = Application::factory()->create();

        livewire(ListApplications::class)
            ->callAction(TestAction::make('Duplicate')->table($application))
            ->assertHasNoFormErrors();

        $duplicatedApplication = Application::query()->whereKeyNot($application->getKey())->firstOrFail();

        expect($duplicatedApplication->root_id)->toBe($duplicatedApplication->getKey())
            ->and($duplicatedApplication->root_id)->not->toBe($application->root_id)
            ->and($duplicatedApplication->latestVersion()?->is($duplicatedApplication))->toBeTrue()
            ->and($application->latestVersion()?->is($application))->toBeTrue();
    });

    it('does not show the original application submissions count on the duplicated application', function () {
        $application = Application::factory()->create();

        $submissionCount = $application->submissions()->count();

        expect($submissionCount)->toBeGreaterThan(0);

        livewire(ListApplications::class)
            ->callAction(TestAction::make('Duplicate')->table($application))
            ->assertHasNoFormErrors();

        $duplicatedApplication = Application::query()->whereKeyNot($application->getKey())->firstOrFail();

        livewire(ListApplications::class)
            ->assertTableColumnStateSet('submissions_count', $submissionCount, record: $application)
            ->assertTableColumnStateSet('submissions_count', 0, record: $duplicatedApplication);
    });

    it('copies the application and step content images to the duplicated application', function () {
        Storage::fake('s3-public');

        $application = Application::factory()->create();

        $applicationImage = $application->addMedia(UploadedFile::fake()->image('application.png'))->toMediaCollection('content', 's3-public');
        $applicationContent = $application->content;
        $applicationContent['content'][] = ['type' => 'image', 'attrs' => ['id' => $applicationImage->uuid]];
        $application->update(['content' => $applicationContent]);

        $step = $application->steps()->create(['label' => 'Step 1', 'sort' => 1, 'content' => ['type' => 'doc', 'content' => []]]);
        $stepImage = $step->addMedia(UploadedFile::fake()->image('step.png'))->toMediaCollection('content', 's3-public');
        $step->update(['content' => ['type' => 'doc', 'content' => [['type' => 'image', 'attrs' => ['id' => $stepImage->uuid]]]]]);

        livewire(ListApplications::class)
            ->callAction(TestAction::make('Duplicate')->table($application))
            ->assertHasNoFormErrors();

        $duplicatedApplication = Application::query()->whereKeyNot($application->getKey())->firstOrFail();
        $duplicatedStep = $duplicatedApplication->steps()->firstOrFail();

        $duplicatedApplicationImage = $duplicatedApplication->getFirstMedia('content');
        $duplicatedStepImage = $duplicatedStep->getFirstMedia('content');

        expect($duplicatedApplicationImage)->not->toBeNull()
            ->and($duplicatedApplicationImage->uuid)->not->toBe($applicationImage->uuid)
            ->and(json_encode($duplicatedApplication->content))->toContain($duplicatedApplicationImage->uuid)->not->toContain($applicationImage->uuid)
            ->and($duplicatedStepImage)->not->toBeNull()
            ->and($duplicatedStepImage->uuid)->not->toBe($stepImage->uuid)
            ->and(json_encode($duplicatedStep->content))->toContain($duplicatedStepImage->uuid)->not->toContain($stepImage->uuid)
            ->and($application->refresh()->getMedia('content')->pluck('uuid')->all())->toBe([$applicationImage->uuid])
            ->and(json_encode($application->content))->toContain($applicationImage->uuid)
            ->and($step->refresh()->getMedia('content')->pluck('uuid')->all())->toBe([$stepImage->uuid])
            ->and(json_encode($step->content))->toContain($stepImage->uuid);

        Storage::disk('s3-public')->assertExists($duplicatedApplicationImage->getPathRelativeToRoot());
        Storage::disk('s3-public')->assertExists($duplicatedStepImage->getPathRelativeToRoot());
    });
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

test('submissions count does not include archived submissions', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $application = Application::factory()->create();

    $totalSubmissions = $application->submissions()->count();

    $application->submissions()
        ->limit((int) ($totalSubmissions / 2))
        ->update(['archived_at' => now()]);

    $expectedCount = $totalSubmissions - (int) ($totalSubmissions / 2);

    livewire(ListApplications::class)
        ->assertTableColumnStateSet('submissions_count', $expectedCount, $application);
});

it('archive bulk action archives all selected applications', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    asSuperAdmin();

    $applicationWithSubmissions = Application::factory()->create();

    ApplicationSubmission::factory()->create([
        'application_id' => $applicationWithSubmissions->id,
    ]);

    $applicationWithoutSubmissions = Application::factory()->create();
    $applicationWithoutSubmissions->submissions()->delete();

    $records = collect([$applicationWithSubmissions, $applicationWithoutSubmissions]);

    livewire(ListApplications::class)
        ->selectTableRecords($records->pluck('id')->all())
        ->callAction(TestAction::make('archive')->table()->bulk())
        ->assertNotified();

    expect($applicationWithSubmissions->fresh()->archived_at)->not->toBeNull();
    expect($applicationWithoutSubmissions->fresh()->archived_at)->not->toBeNull();
});
