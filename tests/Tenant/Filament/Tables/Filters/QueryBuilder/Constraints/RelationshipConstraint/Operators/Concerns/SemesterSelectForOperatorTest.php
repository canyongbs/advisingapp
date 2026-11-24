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

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\EqualsOperatorWithEnrollmentSemester;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperatorWithEnrollmentSemester;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperatorWithEnrollmentSemester;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperatorWithEnrollmentSemester;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Illuminate\Support\Str;

$createEnrollment = fn (Student $student, array $attributes): Enrollment => Enrollment::factory()->create([
    'id' => (string) Str::uuid(),
    'sisid' => $student->sisid,
    'sis_term_id' => null,
    'semester_name' => null,
    ...$attributes,
]);

$filterStudents = fn (string $operatorClass, array $settings, bool $isInverse = false): array => $operatorClass::make()
    ->constraint(RelationshipConstraint::make('enrollments'))
    ->settings($settings)
    ->inverse($isInverse)
    ->applyToBaseQuery(Student::query())
    ->pluck('sisid')
    ->all();

it('counts enrollments by the name of their matched term, ignoring case', function () use ($createEnrollment, $filterStudents) {
    $fallTerm = Term::factory()->create(['name' => 'FA-20']);
    $springTerm = Term::factory()->create(['name' => 'SP-21']);

    $matchingStudent = Student::factory()->create();
    $createEnrollment($matchingStudent, ['sis_term_id' => $fallTerm->sis_term_id]);
    $createEnrollment($matchingStudent, ['semester_name' => 'fa-20']);

    $otherStudent = Student::factory()->create();
    $createEnrollment($otherStudent, ['sis_term_id' => $fallTerm->sis_term_id]);
    $createEnrollment($otherStudent, ['sis_term_id' => $springTerm->sis_term_id, 'semester_name' => 'FA-20']);

    expect($filterStudents(EqualsOperatorWithEnrollmentSemester::class, ['count' => 2, 'semesters' => ['fa-20']]))
        ->toBe([$matchingStudent->sisid]);
});

it('counts enrollments without a matched term as `Unassigned`', function () use ($createEnrollment, $filterStudents) {
    $term = Term::factory()->create(['name' => 'FA-20']);

    $unassignedStudent = Student::factory()->create();
    $createEnrollment($unassignedStudent, ['semester_name' => 'Unmatched']);

    $assignedStudent = Student::factory()->create();
    $createEnrollment($assignedStudent, ['sis_term_id' => $term->sis_term_id]);

    expect($filterStudents(EqualsOperatorWithEnrollmentSemester::class, ['count' => 1, 'semesters' => [EqualsOperatorWithEnrollmentSemester::UNASSIGNED_SEMESTER]]))
        ->toBe([$unassignedStudent->sisid]);
});

it('counts enrollments in any of the selected semesters, including `Unassigned`', function () use ($createEnrollment, $filterStudents) {
    $fallTerm = Term::factory()->create(['name' => 'FA-20']);
    $springTerm = Term::factory()->create(['name' => 'SP-21']);

    $student = Student::factory()->create();
    $createEnrollment($student, ['sis_term_id' => $fallTerm->sis_term_id]);
    $createEnrollment($student, ['semester_name' => 'Unmatched']);
    $createEnrollment($student, ['sis_term_id' => $springTerm->sis_term_id]);

    expect($filterStudents(EqualsOperatorWithEnrollmentSemester::class, ['count' => 2, 'semesters' => ['FA-20', EqualsOperatorWithEnrollmentSemester::UNASSIGNED_SEMESTER]]))
        ->toBe([$student->sisid]);
});

it('finds students without enrollments in the selected semesters', function () use ($createEnrollment, $filterStudents) {
    $term = Term::factory()->create(['name' => 'FA-20']);

    $enrolledStudent = Student::factory()->create();
    $createEnrollment($enrolledStudent, ['sis_term_id' => $term->sis_term_id]);

    $unenrolledStudent = Student::factory()->create();
    $createEnrollment($unenrolledStudent, ['semester_name' => 'Unmatched']);

    expect($filterStudents(IsEmptyOperatorWithEnrollmentSemester::class, ['semesters' => ['FA-20']]))
        ->toBe([$unenrolledStudent->sisid]);
});

it('compares a count of 0 enrollments in the selected semesters', function (string $operatorClass, bool $isInverse, array $expectedStudents) use ($createEnrollment, $filterStudents) {
    $term = Term::factory()->create(['name' => 'FA-20']);

    $students = [
        'enrolled' => Student::factory()->create(),
        'unenrolled' => Student::factory()->create(),
    ];

    $createEnrollment($students['enrolled'], ['sis_term_id' => $term->sis_term_id]);
    $createEnrollment($students['unenrolled'], ['semester_name' => 'Unmatched']);

    expect($filterStudents($operatorClass, ['count' => '0', 'semesters' => ['FA-20']], $isInverse))
        ->toEqualCanonicalizing(array_map(fn (string $student): string => $students[$student]->sisid, $expectedStudents));
})->with([
    'has 0' => [EqualsOperatorWithEnrollmentSemester::class, false, ['unenrolled']],
    'does not have 0' => [EqualsOperatorWithEnrollmentSemester::class, true, ['enrolled']],
    'has maximum 0' => [HasMaxOperatorWithEnrollmentSemester::class, false, ['unenrolled']],
    'has more than 0' => [HasMaxOperatorWithEnrollmentSemester::class, true, ['enrolled']],
    'has minimum 0' => [HasMinOperatorWithEnrollmentSemester::class, false, ['enrolled', 'unenrolled']],
    'has less than 0' => [HasMinOperatorWithEnrollmentSemester::class, true, []],
]);

it('checks whether students have matching enrollments rather than counting them when only that matters', function (string $operatorClass, bool $isInverse, int $count) {
    $sql = $operatorClass::make()
        ->constraint(RelationshipConstraint::make('enrollments'))
        ->settings(['count' => $count])
        ->inverse($isInverse)
        ->applyToBaseQuery(Student::query())
        ->toSql();

    expect($sql)->toContain('exists (')
        ->and($sql)->not->toContain('count(*)');
})->with([
    'has 0' => [EqualsOperatorWithEnrollmentSemester::class, false, 0],
    'does not have 0' => [EqualsOperatorWithEnrollmentSemester::class, true, 0],
    'has maximum 0' => [HasMaxOperatorWithEnrollmentSemester::class, false, 0],
    'has more than 0' => [HasMaxOperatorWithEnrollmentSemester::class, true, 0],
]);

it('ignores a count left over from a previously selected operator when checking for enrollments in the selected semesters', function () use ($createEnrollment, $filterStudents) {
    $term = Term::factory()->create(['name' => 'FA-20']);

    $enrolledStudent = Student::factory()->create();
    $createEnrollment($enrolledStudent, ['sis_term_id' => $term->sis_term_id]);

    $unenrolledStudent = Student::factory()->create();

    expect($filterStudents(IsEmptyOperatorWithEnrollmentSemester::class, ['count' => '0', 'semesters' => ['FA-20']]))
        ->toBe([$unenrolledStudent->sisid])
        ->and($filterStudents(IsEmptyOperatorWithEnrollmentSemester::class, ['count' => '0', 'semesters' => ['FA-20']], isInverse: true))
        ->toBe([$enrolledStudent->sisid]);
});

it('offers the term names, most recent first, and `Unassigned` as semesters', function () {
    Term::factory()->create(['name' => 'FA-20', 'start_date' => '2020-09-01']);
    Term::factory()->create(['name' => 'SP-21', 'start_date' => '2021-01-15']);
    Term::factory()->create(['name' => '2024', 'start_date' => '2020-01-15']);
    Term::factory()->create(['name' => 'fa-20', 'start_date' => '2019-09-01']);
    Term::factory()->create(['name' => '02024', 'start_date' => '2019-01-15']);
    Term::factory()->create(['name' => 'SU-21', 'start_date' => '2021-06-01', 'deleted_at' => now()]);

    expect(EqualsOperatorWithEnrollmentSemester::semesterSelect()->getOptions())->toBe([
        'SP-21' => 'SP-21',
        'FA-20' => 'FA-20',
        '2024' => '2024',
        '02024' => '02024',
        EqualsOperatorWithEnrollmentSemester::UNASSIGNED_SEMESTER => 'Unassigned',
    ]);
});

it('summarizes `Unassigned` by its label', function () {
    $summary = EqualsOperatorWithEnrollmentSemester::make()
        ->constraint(RelationshipConstraint::make('enrollments'))
        ->settings(['count' => 1, 'semesters' => ['FA-20', EqualsOperatorWithEnrollmentSemester::UNASSIGNED_SEMESTER]])
        ->getSummary();

    expect($summary)->toEndWith(' in semesters FA-20, Unassigned');
});
