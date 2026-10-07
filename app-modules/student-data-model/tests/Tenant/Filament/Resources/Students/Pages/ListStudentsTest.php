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
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\StudentDataModel\Enums\SisSystem;
use AdvisingApp\StudentDataModel\Enums\StudentTermAttributeField;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use App\Features\TermAttributesFeature;
use App\Models\User;
use CanyonGBS\Common\Filament\Actions\ArchiveBulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can filter students by first generation', function () {
    Student::truncate();

    asSuperAdmin();

    $studentsWithFirstGen = Student::factory()
        ->state([
            'firstgen' => true,
        ])->count(5)->create();

    $studentsWithoutFirstGen = Student::factory()
        ->state([
            'firstgen' => false,
        ])->count(5)->create();

    livewire(ListStudents::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($studentsWithFirstGen->merge($studentsWithoutFirstGen))
        ->filterTable('firstgen', true)
        ->assertCanSeeTableRecords($studentsWithFirstGen)
        ->assertCanNotSeeTableRecords($studentsWithoutFirstGen)
        ->filterTable('firstgen', false)
        ->assertCanSeeTableRecords($studentsWithoutFirstGen)
        ->assertCanNotSeeTableRecords($studentsWithFirstGen);
});

it('renders the CreateAction based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');

    actingAs($user);

    livewire(ListStudents::class)
        ->assertOk()
        ->assertActionHidden(CreateAction::class);

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('student.create');

    livewire(ListStudents::class)
        ->assertOk()
        ->assertActionHidden(CreateAction::class);

    $user->givePermissionTo('student.create');

    livewire(ListStudents::class)
        ->assertOk()
        ->assertActionVisible(CreateAction::class);
});

it('the archive bulk action is gated by the `student.*.delete` permission', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    actingAs($user);

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionHidden(ArchiveBulkAction::class);

    $user->givePermissionTo('student.*.delete');

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionVisible(ArchiveBulkAction::class);
});

it('shows the view action only with the `settings.*.view` permission', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $student = Student::factory()->create();

    livewire(ListStudents::class)
        ->assertTableActionHidden(ViewAction::class, $student);

    $user->givePermissionTo('settings.*.view');

    livewire(ListStudents::class)
        ->assertTableActionVisible(ViewAction::class, $student);
});

it('can filter students by concerns', function () {
    Student::truncate();

    asSuperAdmin();

    $activeStatusConcern = ConcernStatus::factory()
        ->state([
            'name' => 'Active',
            'classification' => SystemConcernStatusClassification::Active,
        ])
        ->create();

    $inprogressStatusConcern = ConcernStatus::factory()
        ->state([
            'name' => 'InProgress',
            'classification' => SystemConcernStatusClassification::Active,
        ])
        ->create();

    $studentWithStatusActive = Student::factory()->create();

    $studentWithStatusInprogress = Student::factory()->create();

    $activeConcerns = Concern::factory()
        ->count(3)
        ->for($studentWithStatusActive, 'concern')
        ->state([
            'status_id' => $activeStatusConcern->getKey(),
        ])
        ->create();

    $inProgressConcerns = Concern::factory()
        ->count(2)
        ->for($studentWithStatusInprogress, 'concern')
        ->state([
            'status_id' => $inprogressStatusConcern->getKey(),
        ])
        ->create();

    $studentsWithoutConcerns = Student::factory()->count(5)->create();

    livewire(ListStudents::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($studentsWithoutConcerns->merge([$studentWithStatusActive, $studentWithStatusInprogress]))
        ->filterTable('concerns', [$activeStatusConcern, $inprogressStatusConcern])
        ->assertCanSeeTableRecords([$studentWithStatusActive, $studentWithStatusInprogress])
        ->assertCanNotSeeTableRecords($studentsWithoutConcerns)
        ->resetTableFilters()
        ->filterTable('concerns', [$activeStatusConcern])
        ->assertCanSeeTableRecords([$studentWithStatusActive])
        ->assertCanNotSeeTableRecords($studentsWithoutConcerns->merge([$studentWithStatusInprogress]))
        ->removeTableFilter('concerns')
        ->assertCanSeeTableRecords($studentsWithoutConcerns->merge([$studentWithStatusActive, $studentWithStatusInprogress]));
});

it('renders the bulk create concern action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionHidden('createConcern');

    $user->givePermissionTo('concern.create');
    $user->givePermissionTo('student.*.update');

    $user->refresh();

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionVisible('createConcern');
});

it('shows bulk assign tags action for authorized user', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');

    actingAs($user);

    $students = Student::factory()->count(5)->create();

    livewire(ListStudents::class)
        ->assertCanSeeTableRecords($students)
        ->assertTableBulkActionHidden('bulkStudentTags');

    $user->givePermissionTo('student.*.update');

    livewire(ListStudents::class)
        ->assertCanSeeTableRecords($students)
        ->assertTableBulkActionVisible('bulkStudentTags');
});

it('renders the bulk create interaction action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionHidden('createInteraction');

    $user->givePermissionTo('student.*.update');

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionVisible('createInteraction');
});

it('shows bulk subscription action for authorized user', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');

    actingAs($user);

    $students = Student::factory()->count(5)->create();

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionHidden('bulkSubscription');

    $user->givePermissionTo('student.*.update');

    livewire(ListStudents::class)
        ->assertCanSeeTableRecords($students)
        ->assertTableBulkActionVisible('bulkSubscription')
        ->assertSuccessful();
});

describe('archiving', function () {
    it('does not list archived students', function () {
        asSuperAdmin();

        $active = Student::factory()->count(3)->create();
        $archived = Student::factory()->count(2)->create();
        $archived->each(fn (Student $student) => $student->archive());

        livewire(ListStudents::class)
            ->assertOk()
            ->assertCanSeeTableRecords($active)
            ->assertCanNotSeeTableRecords($archived);
    });

    it('archives the selected students instead of deleting them', function () {
        asSuperAdmin();

        $studentSettings = app(ManageStudentConfigurationSettings::class);
        $studentSettings->is_enabled = true;
        $studentSettings->save();

        $student = Student::factory()->create();
        Enrollment::factory()->for($student, 'student')->create();

        expect($student->archived_at)->toBeNull();

        livewire(ListStudents::class)
            ->selectTableRecords([$student])
            ->callAction(TestAction::make(ArchiveBulkAction::class)->table()->bulk());

        $student->refresh();

        expect($student->archived_at)->not->toBeNull()
            ->and($student->trashed())->toBeFalse();

        assertDatabaseHas('enrollments', ['sisid' => $student->getKey(), 'deleted_at' => null]);
    });
});

describe('filter options', function () {
    it('does not offer SIS categories that only archived students have', function () {
        asSuperAdmin();

        Student::factory()->create(['sis_category' => 'Active Category']);

        $archived = Student::factory()->create(['sis_category' => 'Archived Only Category']);
        $archived->archive();

        livewire(ListStudents::class)
            ->assertOk()
            ->assertTableFilterExists('sis_category', function (SelectFilter $filter): bool {
                $options = $filter->getOptions();

                expect($options)->toHaveKey('Active Category')
                    ->and($options)->not->toHaveKey('Archived Only Category');

                return true;
            });
    });
});

describe('term attribute filter', function () {
    beforeEach(function () {
        asSuperAdmin();

        $sisSettings = app(StudentInformationSystemSettings::class);
        $sisSettings->is_enabled = true;
        $sisSettings->sis_system = SisSystem::ThesisElements;
        $sisSettings->save();
    });

    it('filters students by term, attribute and value', function () {
        $term = Term::factory()->create();
        $otherTerm = Term::factory()->create();

        $matching = Student::factory()->create();
        StudentTermAttribute::factory()->for($matching)->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Main']);

        $differentValue = Student::factory()->create();
        StudentTermAttribute::factory()->for($differentValue)->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Online']);

        $differentTerm = Student::factory()->create();
        StudentTermAttribute::factory()->for($differentTerm)->create(['sis_term_id' => $otherTerm->sis_term_id, 'campus' => 'Main']);

        livewire(ListStudents::class)
            ->assertCanSeeTableRecords([$matching, $differentValue, $differentTerm])
            ->filterTable('termAttribute', [
                'sis_term_id' => $term->sis_term_id,
                'attribute' => StudentTermAttributeField::Campus->value,
                'value' => 'Main',
            ])
            ->assertCanSeeTableRecords([$matching])
            ->assertCanNotSeeTableRecords([$differentValue, $differentTerm]);
    });

    it('enables the attribute and value fields once the previous fields are selected', function () {
        $term = Term::factory()->create();

        livewire(ListStudents::class)
            ->assertFormFieldDisabled('termAttribute.attribute', 'tableFiltersForm')
            ->assertFormFieldDisabled('termAttribute.value', 'tableFiltersForm')
            ->set('tableFilters.termAttribute.sis_term_id', $term->sis_term_id)
            ->assertFormFieldEnabled('termAttribute.attribute', 'tableFiltersForm')
            ->assertFormFieldDisabled('termAttribute.value', 'tableFiltersForm')
            ->set('tableFilters.termAttribute.attribute', StudentTermAttributeField::Campus->value)
            ->assertFormFieldEnabled('termAttribute.value', 'tableFiltersForm');
    });

    it('is available for tenants using Thesis Elements', function () {
        livewire(ListStudents::class)
            ->assertTableFilterVisible('termAttribute');
    });

    it('is not available for tenants not using Thesis Elements', function (bool $isEnabled, ?SisSystem $sisSystem) {
        $sisSettings = app(StudentInformationSystemSettings::class);
        $sisSettings->is_enabled = $isEnabled;
        $sisSettings->sis_system = $sisSystem;
        $sisSettings->save();

        livewire(ListStudents::class)
            ->assertTableFilterHidden('termAttribute');
    })->with([
        'Ellucian Ethos' => [true, SisSystem::EllucianEthos],
        'SIS disabled' => [false, SisSystem::ThesisElements],
    ]);

    it('is not available while `TermAttributesFeature` is inactive', function () {
        TermAttributesFeature::deactivate();

        livewire(ListStudents::class)
            ->assertTableFilterHidden('termAttribute');
    });
});
