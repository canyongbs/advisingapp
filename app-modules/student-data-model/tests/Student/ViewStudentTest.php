<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Livewire\ManageStudentInformation;
use AdvisingApp\StudentDataModel\Livewire\ManageStudentPremiumFeatures;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentFilesRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentTasksRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentAlertsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentEventsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentCareTeamRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentEngagementRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentInteractionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentSubscriptionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentFormSubmissionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentApplicationSubmissionsRelationManager;

it('requires proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $student = Student::factory()->create();

    actingAs($user);

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk();
});

it('renders the ProgramsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = ProgramsRelationManager::class;

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('program.view-any');

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EnrollmentsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = EnrollmentsRelationManager::class;

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('enrollment.view-any');

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentEngagementRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = StudentEngagementRelationManager::class;

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement.view-any');

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentInteractionsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = StudentInteractionsRelationManager::class;

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('interaction.view-any');

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentFilesRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = StudentFilesRelationManager::class;

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement_file.view-any');

    livewire(ManageStudentInformation::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentInformation())
                ->tap(fn (ManageStudentInformation $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentAlertsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = StudentAlertsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('alert.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentTasksRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = StudentTasksRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('task.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentCareTeamRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = StudentCareTeamRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('care_team.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
})->skip('This test is skipped because the relationship is to Users, we need to change the manager to focus permissions on CareTeam permissions.');

it('renders the StudentSubscriptionsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = StudentSubscriptionsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('subscription.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ViewStudent())
                ->tap(fn (ViewStudent $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
})->skip('This test is skipped because the relationship is to Users, we need to change the manager to focus permissions on Subscription permissions.');

it('renders the StudentFormSubmissionsRelationManager based on Feature access', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->onlineForms = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = StudentFormSubmissionsRelationManager::class;

    livewire(ManageStudentPremiumFeatures::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentPremiumFeatures())
                ->tap(fn (ManageStudentPremiumFeatures $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->onlineForms = true;

    $licenseSettings->save();

    livewire(ManageStudentPremiumFeatures::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentPremiumFeatures())
                ->tap(fn (ManageStudentPremiumFeatures $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentEventsRelationManager based on Feature access', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->eventManagement = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = StudentEventsRelationManager::class;

    livewire(ManageStudentPremiumFeatures::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentPremiumFeatures())
                ->tap(fn (ManageStudentPremiumFeatures $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->eventManagement = true;

    $licenseSettings->save();

    livewire(ManageStudentPremiumFeatures::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentPremiumFeatures())
                ->tap(fn (ManageStudentPremiumFeatures $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentApplicationSubmissionsRelationManager based on Feature access', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->onlineAdmissions = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = StudentApplicationSubmissionsRelationManager::class;

    livewire(ManageStudentPremiumFeatures::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentPremiumFeatures())
                ->tap(fn (ManageStudentPremiumFeatures $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->onlineAdmissions = true;

    $licenseSettings->save();

    livewire(ManageStudentPremiumFeatures::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentPremiumFeatures())
                ->tap(fn (ManageStudentPremiumFeatures $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});
