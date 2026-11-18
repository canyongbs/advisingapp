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
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Features\MatchedEnrolledTermsFeature;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use function Tests\asSuperAdmin;

it('links term attributes by `sisid`', function (): void {
    asSuperAdmin();

    $student = Student::factory()->create();
    $termAttributes = StudentTermAttribute::factory()->count(2)->for($student)->create();
    $otherTermAttribute = StudentTermAttribute::factory()->create();

    expect($student->termAttributes->pluck('sis_term_id')->all())
        ->toEqualCanonicalizing($termAttributes->pluck('sis_term_id')->all())
        ->not->toContain($otherTermAttribute->sis_term_id);
});

describe('archiving', function (): void {
    it('archives a student', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();

        expect($student->archived_at)->toBeNull()
            ->and($student->isArchived())->toBeFalse();

        expect($student->archive())->toBeTrue();

        $student->refresh();

        expect($student->archived_at)->toBeInstanceOf(Carbon::class)
            ->and($student->isArchived())->toBeTrue();
    });

    it('unarchives a student', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();
        $student->archive();

        expect($student->refresh()->isArchived())->toBeTrue();

        expect($student->unarchive())->toBeTrue();

        expect($student->refresh()->archived_at)->toBeNull()
            ->and($student->isArchived())->toBeFalse();
    });

    it('does not soft delete a student when it is archived', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();

        $student->archive();

        expect($student->refresh()->trashed())->toBeFalse();
    });

    it('includes archived students by default', function (): void {
        asSuperAdmin();

        $archived = Student::factory()->create();
        $archived->archive();

        expect(Student::query()->pluck('sisid'))->toContain($archived->getKey());
    });

    it('excludes archived students from `withoutArchived`', function (): void {
        asSuperAdmin();

        $active = Student::factory()->create();
        $archived = Student::factory()->create();
        $archived->archive();

        $sisids = Student::query()->withoutArchived()->pluck('sisid');

        expect($sisids)->toContain($active->getKey())
            ->and($sisids)->not->toContain($archived->getKey());
    });

    it('records one archived audit and no updated audit when a student is archived', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();

        // The factory records its own updated audit when it links primary contact records.
        $updatedAuditCount = $student->audits()->where('event', 'updated')->count();

        expect($student->audits()->where('event', 'archived')->exists())->toBeFalse();

        $student->archive();

        expect($student->audits()->where('event', 'archived')->count())->toBe(1)
            ->and($student->audits()->where('event', 'updated')->count())->toBe($updatedAuditCount);
    });

    it('records one unarchived audit and no updated audit when a student is unarchived', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();
        $student->archive();

        $updatedAuditCount = $student->audits()->where('event', 'updated')->count();

        expect($student->audits()->where('event', 'unarchived')->exists())->toBeFalse();

        $student->unarchive();

        expect($student->audits()->where('event', 'unarchived')->count())->toBe(1)
            ->and($student->audits()->where('event', 'updated')->count())->toBe($updatedAuditCount);
    });
});

describe('term enrollments', function (): void {
    $createEnrollment = fn (Student $student, array $attributes): Enrollment => Enrollment::factory()->for($student, 'student')->create([
        'id' => (string) Str::uuid(),
        'sis_term_id' => null,
        'semester_code' => null,
        'semester_name' => null,
        'start_date' => null,
        ...$attributes,
    ]);

    it('picks the enrollments in the first and most recent matched terms, by term rather than enrollment start date', function () use ($createEnrollment): void {
        $student = Student::factory()->create();

        $earliestTerm = Term::factory()->create(['start_date' => '2019-09-01']);
        $firstTerm = Term::factory()->create(['start_date' => '2020-09-01']);
        $mostRecentTerm = Term::factory()->create(['start_date' => '2021-09-01']);
        Term::factory()->withoutSisTermId()->create(['name' => 'Manual Term']);

        $createEnrollment($student, ['sis_term_id' => $earliestTerm->sis_term_id])->delete();
        $firstTermEnrollment = $createEnrollment($student, ['sis_term_id' => $firstTerm->sis_term_id, 'start_date' => '2022-01-01']);
        $mostRecentTermEnrollment = $createEnrollment($student, ['sis_term_id' => $mostRecentTerm->sis_term_id, 'start_date' => '2018-01-01']);
        $createEnrollment($student, ['semester_name' => 'manual term']);
        $createEnrollment($student, ['semester_code' => '4201', 'semester_name' => 'Unmatched', 'start_date' => '2000-01-01']);
        $createEnrollment($student, ['semester_code' => '4299', 'semester_name' => 'Unmatched', 'start_date' => '2030-01-01']);

        expect($student->firstTermEnrollment->getKey())->toBe($firstTermEnrollment->getKey())
            ->and($student->mostRecentTermEnrollment->getKey())->toBe($mostRecentTermEnrollment->getKey());
    });

    it('falls back to the enrollments with the earliest and latest start dates and a semester code when none is matched to a term', function () use ($createEnrollment): void {
        $student = Student::factory()->create();

        $createEnrollment($student, ['semester_name' => 'Unmatched', 'start_date' => '2000-01-01']);
        $earliestEnrollment = $createEnrollment($student, ['semester_code' => '4201', 'start_date' => '2020-01-01']);
        $latestEnrollment = $createEnrollment($student, ['semester_code' => '4203', 'start_date' => '2021-01-01']);
        $createEnrollment($student, ['semester_code' => '4205']);

        expect($student->firstTermEnrollment->getKey())->toBe($earliestEnrollment->getKey())
            ->and($student->mostRecentTermEnrollment->getKey())->toBe($latestEnrollment->getKey());
    });

    it('eager loads the term enrollments of each student', function () use ($createEnrollment): void {
        $firstTerm = Term::factory()->create(['start_date' => '2020-09-01']);
        $mostRecentTerm = Term::factory()->create(['start_date' => '2021-09-01']);

        $student = Student::factory()->create();
        $firstTermEnrollment = $createEnrollment($student, ['sis_term_id' => $firstTerm->sis_term_id]);
        $mostRecentTermEnrollment = $createEnrollment($student, ['sis_term_id' => $mostRecentTerm->sis_term_id]);

        $otherStudent = Student::factory()->create();
        $otherStudentEnrollment = $createEnrollment($otherStudent, ['sis_term_id' => $firstTerm->sis_term_id]);

        $studentWithoutEnrollments = Student::factory()->create();

        $students = Student::query()
            ->with(['firstTermEnrollment', 'mostRecentTermEnrollment'])
            ->get()
            ->keyBy('sisid');

        expect($students[$student->sisid]->firstTermEnrollment->getKey())->toBe($firstTermEnrollment->getKey())
            ->and($students[$student->sisid]->mostRecentTermEnrollment->getKey())->toBe($mostRecentTermEnrollment->getKey())
            ->and($students[$otherStudent->sisid]->firstTermEnrollment->getKey())->toBe($otherStudentEnrollment->getKey())
            ->and($students[$otherStudent->sisid]->mostRecentTermEnrollment->getKey())->toBe($otherStudentEnrollment->getKey())
            ->and($students[$studentWithoutEnrollments->sisid]->firstTermEnrollment)->toBeNull()
            ->and($students[$studentWithoutEnrollments->sisid]->mostRecentTermEnrollment)->toBeNull();
    });

    it('picks the enrollments with the earliest and latest start dates and a semester code when the feature is inactive', function () use ($createEnrollment): void {
        MatchedEnrolledTermsFeature::deactivate();

        $student = Student::factory()->create();

        $firstTerm = Term::factory()->create(['start_date' => '2020-09-01']);
        $mostRecentTerm = Term::factory()->create(['start_date' => '2021-09-01']);

        $createEnrollment($student, ['sis_term_id' => $firstTerm->sis_term_id, 'start_date' => '2022-01-01']);
        $createEnrollment($student, ['sis_term_id' => $mostRecentTerm->sis_term_id, 'start_date' => '2018-01-01']);
        $earliestEnrollment = $createEnrollment($student, ['semester_code' => '4201', 'start_date' => '2019-01-01']);
        $latestEnrollment = $createEnrollment($student, ['semester_code' => '4203', 'start_date' => '2023-01-01']);

        expect($student->firstTermEnrollment->getKey())->toBe($earliestEnrollment->getKey())
            ->and($student->mostRecentTermEnrollment->getKey())->toBe($latestEnrollment->getKey());
    });
});
