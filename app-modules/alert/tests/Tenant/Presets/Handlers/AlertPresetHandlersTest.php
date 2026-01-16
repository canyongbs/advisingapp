<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Alert\Tests\Tenant;

use AdvisingApp\Alert\Actions\GenerateStudentAlertsView;
use AdvisingApp\Alert\Configurations\AdultLearnerAlertConfiguration;
use AdvisingApp\Alert\Configurations\NewStudentAlertConfiguration;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Models\StudentAlert;
use AdvisingApp\Alert\Presets\AlertPreset;
use AdvisingApp\Concern\Enums\SystemConcernStatusClassification;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    DB::statement('DROP VIEW IF EXISTS student_alerts');
});

it('can identify students with D or F grades (DorfGradePresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $studentWithD = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();

    $studentWithF = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'F']), 'enrollments')
        ->create();

    $studentWithLowercaseD = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'd']), 'enrollments')
        ->create();

    $studentWithA = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'A']), 'enrollments')
        ->create();

    $studentWithB = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'B']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithD->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithF->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithLowercaseD->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithA->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentWithB->sisid)->exists())->toBeFalse();
});

it('can identify students with multiple D or F grades (MultipleDorfGradesPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::MultipleDorfGrades])
        ->enabled()
        ->create();

    $studentWithMultipleDorF = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['crse_grade_off' => 'D'],
                    ['crse_grade_off' => 'F']
                ),
            'enrollments'
        )
        ->create();

    $studentWithSingleD = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();

    $studentWithoutDorF = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'A']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithMultipleDorF->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithSingleD->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentWithoutDorF->sisid)->exists())->toBeFalse();
});

it('can identify students with W grade (CourseWithdrawalPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::CourseWithdrawal])
        ->enabled()
        ->create();

    $studentWithW = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'W']), 'enrollments')
        ->create();

    $studentWithLowercaseW = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'w']), 'enrollments')
        ->create();

    $studentWithA = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'A']), 'enrollments')
        ->create();

    $studentWithD = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithW->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithLowercaseW->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithA->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentWithD->sisid)->exists())->toBeFalse();
});

it('can identify students with multiple W grades (MultipleCourseWithdrawalsPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::MultipleCourseWithdrawals])
        ->enabled()
        ->create();

    $studentWithMultipleW = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['crse_grade_off' => 'W', 'class_nbr' => '12345'],
                    ['crse_grade_off' => 'W', 'class_nbr' => '12346']
                ),
            'enrollments'
        )
        ->create();

    $studentWithSingleW = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'W']), 'enrollments')
        ->create();

    $studentWithoutW = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'A']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithMultipleW->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithSingleW->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentWithoutW->sisid)->exists())->toBeFalse();
});

it('can identify students with repeated course attempts (RepeatedCourseAttemptPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::RepeatedCourseAttempt])
        ->enabled()
        ->create();

    $sameClassNbr = '10001';
    $studentWithRepeatedCourse = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['class_nbr' => $sameClassNbr, 'crse_grade_off' => 'D'],
                    ['class_nbr' => $sameClassNbr, 'crse_grade_off' => 'C']
                ),
            'enrollments'
        )
        ->create();

    $studentWithoutRepeatedCourse = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['class_nbr' => '10002'],
                    ['class_nbr' => '10003']
                ),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithRepeatedCourse->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithoutRepeatedCourse->sisid)->exists())->toBeFalse();
});

it('can identify first generation students (FirstGenerationStudentPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::FirstGenerationStudent])
        ->enabled()
        ->create();

    $firstGenStudent1 = Student::factory()->create([
        'firstgen' => true,
    ]);

    $firstGenStudent2 = Student::factory()->create([
        'firstgen' => 1,
    ]);

    $nonFirstGenStudent1 = Student::factory()->create([
        'firstgen' => false,
    ]);

    $nonFirstGenStudent2 = Student::factory()->create([
        'firstgen' => 0,
    ]);

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $firstGenStudent1->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $firstGenStudent2->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $nonFirstGenStudent1->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $nonFirstGenStudent2->sisid)->exists())->toBeFalse();
});

it('can identify adult learners with default minimum age (AdultLearnerPresetHandler)', function () {
    $config = AdultLearnerAlertConfiguration::factory()->create(['minimum_age' => 24]);

    $alertConfig = AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::AdultLearner,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->enabled()
        ->create();

    $currentYear = now()->year;

    $adultLearner = Student::factory()->create([
        'birthdate' => ($currentYear - 25) . '-01-01',
    ]);

    $studentAtThreshold = Student::factory()->create([
        'birthdate' => ($currentYear - 24) . '-01-01',
    ]);

    $youngStudent = Student::factory()->create([
        'birthdate' => ($currentYear - 20) . '-01-01',
    ]);

    $studentBelowThreshold = Student::factory()->create([
        'birthdate' => ($currentYear - 23) . '-01-01',
    ]);

    $studentNoBirthdate = Student::factory()->create([
        'birthdate' => null,
    ]);

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $adultLearner->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentAtThreshold->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $youngStudent->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentBelowThreshold->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentNoBirthdate->sisid)->exists())->toBeFalse();
});

it('can identify adult learners with custom minimum age (AdultLearnerPresetHandler)', function () {
    $config = AdultLearnerAlertConfiguration::factory()->create(['minimum_age' => 30]);

    $alertConfig = AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::AdultLearner,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->enabled()
        ->create();

    $currentYear = now()->year;

    $olderStudent = Student::factory()->create([
        'birthdate' => ($currentYear - 35) . '-01-01',
    ]);

    $studentAtThreshold = Student::factory()->create([
        'birthdate' => ($currentYear - 30) . '-01-01',
    ]);

    $studentBelowThreshold = Student::factory()->create([
        'birthdate' => ($currentYear - 25) . '-01-01',
    ]);

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $olderStudent->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentAtThreshold->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentBelowThreshold->sisid)->exists())->toBeFalse();
});

it('can identify new students with default semester count (NewStudentPresetHandler)', function () {
    $config = NewStudentAlertConfiguration::factory()->create(['number_of_semesters' => 1]);

    $alertConfig = AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::NewStudent,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->enabled()
        ->create();

    $newStudent = Student::factory()
        ->has(Enrollment::factory()->state(['semester_code' => '4201']), 'enrollments')
        ->create();

    $studentWith2Semesters = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['semester_code' => '4201'],
                    ['semester_code' => '4202']
                ),
            'enrollments'
        )
        ->create();

    $studentWith3Semesters = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(3)
                ->sequence(
                    ['semester_code' => '4201'],
                    ['semester_code' => '4202'],
                    ['semester_code' => '4203']
                ),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $newStudent->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWith2Semesters->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentWith3Semesters->sisid)->exists())->toBeFalse();
});

it('can identify new students with custom semester count (NewStudentPresetHandler)', function () {
    $config = NewStudentAlertConfiguration::factory()->create(['number_of_semesters' => 3]);

    $alertConfig = AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::NewStudent,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->enabled()
        ->create();

    $studentWith1Semester = Student::factory()
        ->has(Enrollment::factory()->state(['semester_code' => '4201']), 'enrollments')
        ->create();

    $studentWith3Semesters = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(3)
                ->sequence(
                    ['semester_code' => '4201'],
                    ['semester_code' => '4202'],
                    ['semester_code' => '4203']
                ),
            'enrollments'
        )
        ->create();

    $studentWith4Semesters = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(4)
                ->sequence(
                    ['semester_code' => '4201'],
                    ['semester_code' => '4202'],
                    ['semester_code' => '4203'],
                    ['semester_code' => '4204']
                ),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWith1Semester->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWith3Semesters->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWith4Semesters->sisid)->exists())->toBeFalse();
});

it('correctly handles multiple enabled alert configurations simultaneously', function () {
    $dorfConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $withdrawalConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::CourseWithdrawal])
        ->enabled()
        ->create();

    $firstGenConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::FirstGenerationStudent])
        ->enabled()
        ->create();

    $multiAlertStudent = Student::factory()
        ->state(['firstgen' => true])
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['crse_grade_off' => 'D'],
                    ['crse_grade_off' => 'W']
                ),
            'enrollments'
        )
        ->create();

    $singleAlertStudent = Student::factory()
        ->state(['firstgen' => false])
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $multiAlertStudent->sisid,
        'alert_configuration_id' => $dorfConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $multiAlertStudent->sisid,
        'alert_configuration_id' => $withdrawalConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $multiAlertStudent->sisid,
        'alert_configuration_id' => $firstGenConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $singleAlertStudent->sisid,
        'alert_configuration_id' => $dorfConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $singleAlertStudent->sisid)->count())->toBe(1);
    expect(StudentAlert::where('sisid', $multiAlertStudent->sisid)->count())->toBe(3);
});

it('only includes enabled alert configurations in the view', function () {
    $enabledConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $disabledConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::CourseWithdrawal])
        ->disabled()
        ->create();

    $student = Student::factory()
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['crse_grade_off' => 'D'],
                    ['crse_grade_off' => 'W']
                ),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $student->sisid,
        'alert_configuration_id' => $enabledConfig->id,
    ]);

    expect(StudentAlert::where('alert_configuration_id', $disabledConfig->id)->exists())->toBeFalse();
});

it('creates an empty view when no alert configurations are enabled', function () {
    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->disabled()
        ->create();

    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::CourseWithdrawal])
        ->disabled()
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::count())->toBe(0);

    $result = DB::select('SELECT * FROM student_alerts');
    expect($result)->toBeArray()->toBeEmpty();
});

it('excludes soft-deleted enrollments from alert detection', function () {
    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $student = Student::factory()->create([
        'sisid' => 'TEST123',
        'first' => 'Test',
        'last' => 'Student',
        'full_name' => 'Test Student',
    ]);

    Enrollment::factory()
        ->for($student, 'student')
        ->create([
            'crse_grade_off' => 'D',
            'deleted_at' => now(),
        ]);

    expect(Enrollment::where('sisid', 'TEST123')->count())->toBe(0);
    expect(Enrollment::withTrashed()->where('sisid', 'TEST123')->count())->toBe(1);

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::where('sisid', 'TEST123')->exists())->toBeFalse();
});

it('excludes soft-deleted students from alert detection', function () {
    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::FirstGenerationStudent])
        ->enabled()
        ->create();

    $student = Student::factory()->create([
        'firstgen' => true,
    ]);
    $student->delete();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::where('sisid', $student->sisid)->exists())->toBeFalse();
});

it('has correct StudentAlert model relationships', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $student = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $studentAlert = StudentAlert::where('sisid', $student->sisid)->first();

    expect($studentAlert)->not->toBeNull();

    expect($studentAlert->student)->not->toBeNull();
    expect($studentAlert->student->sisid)->toBe($student->sisid);
    expect($studentAlert->student)->toBeInstanceOf(Student::class);

    expect($studentAlert->alertConfiguration)->not->toBeNull();
    expect($studentAlert->alertConfiguration->id)->toBe($alertConfig->id);
    expect($studentAlert->alertConfiguration)->toBeInstanceOf(AlertConfiguration::class);

    $alerts = StudentAlert::where('sisid', $student->sisid)->get();
    expect($alerts->count())->toBe(1);
    expect($alerts->first()->alert_configuration_id)->toBe($alertConfig->id);
});

it('has correct inverse relationships from Student and AlertConfiguration', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $student = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $studentAlerts = $student->studentAlerts;
    expect($studentAlerts)->toHaveCount(1);
    expect($studentAlerts->first()->alert_configuration_id)->toBe($alertConfig->id);

    $configAlerts = $alertConfig->studentAlerts;
    expect($configAlerts)->toHaveCount(1);
    expect($configAlerts->first()->sisid)->toBe($student->sisid);
});

it('excludes students with null semester_code from new student detection', function () {
    $config = NewStudentAlertConfiguration::factory()->create(['number_of_semesters' => 1]);

    AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::NewStudent,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->enabled()
        ->create();

    $studentWithNullSemester = Student::factory()
        ->has(Enrollment::factory()->state(['semester_code' => null]), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::where('sisid', $studentWithNullSemester->sisid)->exists())->toBeFalse();
});

it('regenerates view correctly when execute is called multiple times', function () {
    $config1 = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::count())->toBe(1);

    $config1->update(['is_enabled' => false]);
    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::count())->toBe(0);
});

it('excludes students with no enrollments from enrollment-based alerts', function () {
    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $studentWithoutEnrollments = Student::factory()->create();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::where('sisid', $studentWithoutEnrollments->sisid)->exists())->toBeFalse();
});

it('handles empty database scenario (no students at all)', function () {
    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::FirstGenerationStudent])
        ->enabled()
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::count())->toBe(0);

    $result = DB::select('SELECT * FROM student_alerts');
    expect($result)->toBeArray()->toBeEmpty();
});

it('handles scenario where alerts are enabled but no students match criteria', function () {
    AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    Student::factory()
        ->count(5)
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'A']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::count())->toBe(0);

    $result = DB::select('SELECT * FROM student_alerts');
    expect($result)->toBeArray()->toBeEmpty();
});

it('can identify students with active concerns (ConcernRaisedPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::ConcernRaised])
        ->enabled()
        ->create();

    $activeStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
        'name' => 'Active',
    ]);

    $resolvedStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Resolved,
        'name' => 'Resolved',
    ]);

    $canceledStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Canceled,
        'name' => 'Canceled',
    ]);

    $studentWithActiveConcern = Student::factory()->create();
    Concern::factory()->create([
        'concern_type' => $studentWithActiveConcern->getMorphClass(),
        'concern_id' => $studentWithActiveConcern->getKey(),
        'status_id' => $activeStatus->id,
        'description' => 'Test active concern',
    ]);

    $studentWithResolvedConcern = Student::factory()->create();
    Concern::factory()->create([
        'concern_type' => $studentWithResolvedConcern->getMorphClass(),
        'concern_id' => $studentWithResolvedConcern->getKey(),
        'status_id' => $resolvedStatus->id,
        'description' => 'Test resolved concern',
    ]);

    $studentWithCanceledConcern = Student::factory()->create();
    Concern::factory()->create([
        'concern_type' => $studentWithCanceledConcern->getMorphClass(),
        'concern_id' => $studentWithCanceledConcern->getKey(),
        'status_id' => $canceledStatus->id,
        'description' => 'Test canceled concern',
    ]);

    $studentWithNoConcern = Student::factory()->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithActiveConcern->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithActiveConcern->sisid)->exists())->toBeTrue();
    expect(StudentAlert::where('sisid', $studentWithResolvedConcern->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentWithCanceledConcern->sisid)->exists())->toBeFalse();
    expect(StudentAlert::where('sisid', $studentWithNoConcern->sisid)->exists())->toBeFalse();
});

it('can identify students with multiple active concerns (ConcernRaisedPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::ConcernRaised])
        ->enabled()
        ->create();

    $activeStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
        'name' => 'Active',
    ]);

    $studentWithMultipleConcerns = Student::factory()->create();
    Concern::factory()
        ->count(3)
        ->create([
            'concern_type' => $studentWithMultipleConcerns->getMorphClass(),
            'concern_id' => $studentWithMultipleConcerns->getKey(),
            'status_id' => $activeStatus->id,
        ]);

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithMultipleConcerns->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithMultipleConcerns->sisid)->count())->toBe(1);
});

it('excludes soft-deleted concerns from concern raised detection (ConcernRaisedPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::ConcernRaised])
        ->enabled()
        ->create();

    $activeStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
        'name' => 'Active',
    ]);

    $studentWithDeletedConcern = Student::factory()->create();
    $concern = Concern::factory()->create([
        'concern_type' => $studentWithDeletedConcern->getMorphClass(),
        'concern_id' => $studentWithDeletedConcern->getKey(),
        'status_id' => $activeStatus->id,
        'description' => 'Test concern to be deleted',
    ]);
    $concern->delete();

    app(GenerateStudentAlertsView::class)->execute();

    expect(StudentAlert::where('sisid', $studentWithDeletedConcern->sisid)->exists())->toBeFalse();
});

it('identifies students with concerns of various active classifications (ConcernRaisedPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::ConcernRaised])
        ->enabled()
        ->create();

    $activeStatus1 = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
        'name' => 'New',
    ]);

    $activeStatus2 = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
        'name' => 'In Progress',
    ]);

    $student1 = Student::factory()->create();
    Concern::factory()->create([
        'concern_type' => $student1->getMorphClass(),
        'concern_id' => $student1->getKey(),
        'status_id' => $activeStatus1->id,
    ]);

    $student2 = Student::factory()->create();
    Concern::factory()->create([
        'concern_type' => $student2->getMorphClass(),
        'concern_id' => $student2->getKey(),
        'status_id' => $activeStatus2->id,
    ]);

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $student1->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $student2->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);
});

it('handles students with mixed concern statuses (ConcernRaisedPresetHandler)', function () {
    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::ConcernRaised])
        ->enabled()
        ->create();

    $activeStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
        'name' => 'Active',
    ]);

    $resolvedStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Resolved,
        'name' => 'Resolved',
    ]);

    $studentWithMixedConcerns = Student::factory()->create();
    Concern::factory()->create([
        'concern_type' => $studentWithMixedConcerns->getMorphClass(),
        'concern_id' => $studentWithMixedConcerns->getKey(),
        'status_id' => $activeStatus->id,
        'description' => 'Active concern',
    ]);
    Concern::factory()->create([
        'concern_type' => $studentWithMixedConcerns->getMorphClass(),
        'concern_id' => $studentWithMixedConcerns->getKey(),
        'status_id' => $resolvedStatus->id,
        'description' => 'Resolved concern',
    ]);

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithMixedConcerns->sisid,
        'alert_configuration_id' => $alertConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithMixedConcerns->sisid)->count())->toBe(1);
});

it('correctly combines concern raised alerts with other alert types', function () {
    $concernAlertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::ConcernRaised])
        ->enabled()
        ->create();

    $dorfConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $activeStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
        'name' => 'Active',
    ]);

    $studentWithBoth = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'D']), 'enrollments')
        ->create();
    Concern::factory()->create([
        'concern_type' => $studentWithBoth->getMorphClass(),
        'concern_id' => $studentWithBoth->getKey(),
        'status_id' => $activeStatus->id,
    ]);

    $studentWithConcernOnly = Student::factory()->create();
    Concern::factory()->create([
        'concern_type' => $studentWithConcernOnly->getMorphClass(),
        'concern_id' => $studentWithConcernOnly->getKey(),
        'status_id' => $activeStatus->id,
    ]);

    $studentWithGradeOnly = Student::factory()
        ->has(Enrollment::factory()->state(['crse_grade_off' => 'F']), 'enrollments')
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithBoth->sisid,
        'alert_configuration_id' => $concernAlertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithBoth->sisid,
        'alert_configuration_id' => $dorfConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithConcernOnly->sisid,
        'alert_configuration_id' => $concernAlertConfig->id,
    ]);

    assertDatabaseHas('student_alerts', [
        'sisid' => $studentWithGradeOnly->sisid,
        'alert_configuration_id' => $dorfConfig->id,
    ]);

    expect(StudentAlert::where('sisid', $studentWithBoth->sisid)->count())->toBe(2);
    expect(StudentAlert::where('sisid', $studentWithConcernOnly->sisid)->count())->toBe(1);
    expect(StudentAlert::where('sisid', $studentWithGradeOnly->sisid)->count())->toBe(1);
});
