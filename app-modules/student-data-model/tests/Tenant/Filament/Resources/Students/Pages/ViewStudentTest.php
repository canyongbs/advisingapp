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

use AdvisingApp\Concern\Enums\SystemConcernStatusClassification;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\StudentDataModel\Enums\SisSystem;
use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableActivityFeedWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableCareTeamWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableConcernsWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableSubscriptionsWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableTasksWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Actions\StudentTagsAction;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\ApplicationSubmissionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EngagementFilesRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EngagementsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EnrollmentsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EventsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\FormSubmissionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\InteractionsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\ProgramsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\TermAttributesRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\Term;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use App\Features\MatchedEnrolledTermsFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('requires proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $student = Student::factory()->create();

    actingAs($user);

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk();
});

it('renders the EducatableActivityFeedWidget based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableActivityFeedWidget::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('engagement.view-any');
    $user->givePermissionTo('engagement.*.view');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the ProgramsRelationManager based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = ProgramsRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('program.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EnrollmentsRelationManager based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = EnrollmentsRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('enrollment.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the TermAttributesRelationManager based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('enrollment.view-any');

    actingAs($user);

    $relationManager = TermAttributesRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $sisSettings = app(StudentInformationSystemSettings::class);
    $sisSettings->is_enabled = true;
    $sisSettings->sis_system = SisSystem::ThesisElements;
    $sisSettings->save();

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EngagementsRelationManager based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = EngagementsRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);

    $user->revokePermissionTo('engagement.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement_response.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);

    $user->givePermissionTo('engagement.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the InteractionsRelationManager based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = InteractionsRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('interaction.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EngagementFilesRelationManager based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = EngagementFilesRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('engagement_file.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EducatableConcernsWidget based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
    ]);
    $widget = EducatableConcernsWidget::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('concern.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the EducatableTasksWidget based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableTasksWidget::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('task.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the EducatableCareTeamWidget based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableCareTeamWidget::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('care_team.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the EducatableSubscriptionsWidget based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $widget = EducatableSubscriptionsWidget::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($widget);

    $user->givePermissionTo('subscription.view-any');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($widget);
});

it('renders the FormSubmissionsRelationManager based on Feature access', function (): void {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->onlineForms = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = FormSubmissionsRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->onlineForms = true;

    $licenseSettings->save();

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the EventsRelationManager based on Feature access', function (): void {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->eventManagement = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = EventsRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->eventManagement = true;

    $licenseSettings->save();

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the ApplicationSubmissionsRelationManager based on Feature access', function (): void {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->onlineAdmissions = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = ApplicationSubmissionsRelationManager::class;

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $licenseSettings->data->addons->onlineAdmissions = true;

    $licenseSettings->save();

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders the StudentTagsAction based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertActionHidden(StudentTagsAction::class);

    $user->givePermissionTo('student.*.update');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertActionVisible(StudentTagsAction::class);
});

it('renders the SyncStudentSisAction based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertActionHidden('refreshSis');

    $sisSettings = app(StudentInformationSystemSettings::class);
    $sisSettings->is_enabled = true;
    $sisSettings->sis_system = SisSystem::ThesisElements;
    $sisSettings->save();

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertActionVisible('refreshSis');
});

it('renders the `ArchiveAction` based on proper access', function (): void {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('student.*.delete');

    actingAs($user);

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertActionHidden(ArchiveAction::class);

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('student.*.delete');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertActionHidden(ArchiveAction::class);

    $user->givePermissionTo('student.*.delete');

    Livewire::test(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertActionVisible(ArchiveAction::class);
});

describe('archiving', function (): void {
    // The resource query excludes archived students, so the page cannot resolve them at all:
    // a link that was valid before they were archived, or a URL typed by hand, gets a 404.
    it('cannot be opened once the student is archived', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();

        Livewire::test(ViewStudent::class, ['record' => $student->getKey()])
            ->assertOk();

        $student->archive();

        Livewire::test(ViewStudent::class, ['record' => $student->getKey()])
            ->assertNotFound();
    });

    it('returns not found for a direct URL to an archived student', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();
        $url = StudentResource::getUrl('view', ['record' => $student]);

        $student->archive();

        get($url)->assertNotFound();
    });
});

describe('profile terms', function (): void {
    $createStudentWithEnrollments = function (): Student {
        $firstTerm = Term::factory()->create(['name' => 'FA-20', 'start_date' => '2020-09-01']);
        $mostRecentTerm = Term::factory()->create(['name' => 'SP-21', 'start_date' => '2021-01-15']);

        $student = Student::factory()->create();

        Enrollment::factory()->for($student, 'student')->create([
            'sis_term_id' => $firstTerm->sis_term_id,
            'semester_code' => '4201',
            'semester_name' => 'Fall Semester 2020',
            'start_date' => '2020-09-01',
        ]);

        Enrollment::factory()->for($student, 'student')->create([
            'sis_term_id' => $mostRecentTerm->sis_term_id,
            'semester_code' => '4202',
            'semester_name' => 'Spring Semester 2021',
            'start_date' => '2021-01-15',
        ]);

        return $student;
    };

    it('shows the display names of the first and most recent matched terms', function () use ($createStudentWithEnrollments): void {
        asSuperAdmin();

        $student = $createStudentWithEnrollments();

        Livewire::test(ViewStudent::class, ['record' => $student->getKey()])
            ->assertOk()
            ->assertSchemaComponentStateSet('firstTermEnrollment.semester_name', 'FA-20 (Sep 2020)', 'profile')
            ->assertSchemaComponentStateSet('mostRecentTermEnrollment.semester_name', 'SP-21 (Jan 2021)', 'profile');
    });

    it('shows the semester names of the first and most recent enrollments when they are not matched to a term', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();

        Enrollment::factory()->for($student, 'student')->create([
            'sis_term_id' => null,
            'semester_code' => '4201',
            'semester_name' => 'Fall Semester 2020',
            'start_date' => '2020-09-01',
        ]);

        Enrollment::factory()->for($student, 'student')->create([
            'sis_term_id' => null,
            'semester_code' => '4202',
            'semester_name' => 'Spring Semester 2021',
            'start_date' => '2021-01-15',
        ]);

        Livewire::test(ViewStudent::class, ['record' => $student->getKey()])
            ->assertOk()
            ->assertSchemaComponentStateSet('firstTermEnrollment.semester_name', 'Fall Semester 2020', 'profile')
            ->assertSchemaComponentStateSet('mostRecentTermEnrollment.semester_name', 'Spring Semester 2021', 'profile');
    });

    it('shows the semester names of the first and most recent enrollments when the feature is inactive', function () use ($createStudentWithEnrollments): void {
        MatchedEnrolledTermsFeature::deactivate();

        asSuperAdmin();

        $student = $createStudentWithEnrollments();

        Livewire::test(ViewStudent::class, ['record' => $student->getKey()])
            ->assertOk()
            ->assertSchemaComponentStateSet('firstTermEnrollment.semester_name', 'Fall Semester 2020', 'profile')
            ->assertSchemaComponentStateSet('mostRecentTermEnrollment.semester_name', 'Spring Semester 2021', 'profile');
    });
});
