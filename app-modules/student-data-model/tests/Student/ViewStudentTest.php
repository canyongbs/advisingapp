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

use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets\EducatableTasksWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets\EducatableAlertsWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets\EducatableCareTeamWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\EventsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets\EducatableActivityFeedWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets\EducatableSubscriptionsWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\InteractionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementFilesRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\FormSubmissionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\ApplicationSubmissionsRelationManager;

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

it('renders the EducatableActivityFeedWidget based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableActivityFeedWidget::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('timeline.access');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the ProgramsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = ProgramsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('program.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
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

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('enrollment.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EngagementsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = EngagementsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);

    $user->revokePermissionTo('engagement.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement_response.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);

    $user->givePermissionTo('engagement.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the InteractionsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = InteractionsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('interaction.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EngagementFilesRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = EngagementFilesRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement_file.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EducatableAlertsWidget based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    AlertStatus::factory()->create([
        'classification' => SystemAlertStatusClassification::Active,
    ]);
    $widget = EducatableAlertsWidget::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('alert.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the EducatableTasksWidget based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableTasksWidget::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('task.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the EducatableCareTeamWidget based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableCareTeamWidget::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('care_team.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the EducatableSubscriptionsWidget based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableSubscriptionsWidget::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('subscription.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the FormSubmissionsRelationManager based on Feature access', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->onlineForms = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = FormSubmissionsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->onlineForms = true;

    $licenseSettings->save();

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EventsRelationManager based on Feature access', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->eventManagement = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = EventsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->eventManagement = true;

    $licenseSettings->save();

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the ApplicationSubmissionsRelationManager based on Feature access', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->onlineAdmissions = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = ApplicationSubmissionsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->onlineAdmissions = true;

    $licenseSettings->save();

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});
