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

use AdvisingApp\StudentDataModel\Actions\CompleteStudentDataImport;
use AdvisingApp\StudentDataModel\Actions\CreateTemporaryStudentDataImportTables;
use AdvisingApp\StudentDataModel\Filament\Imports\EnrollmentImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentImporter;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\StudentDataImport;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

$createImport = function (User $user, string $importer): Import {
    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'import.csv';
    $import->file_path = 'imports/import.csv';
    $import->importer = $importer;
    $import->total_rows = 1;
    $import->successful_rows = 1;
    $import->save();

    return $import;
};

$createStudentDataImportWithEnrollment = function (array $enrollmentAttributes) use ($createImport): StudentDataImport {
    $user = User::factory()->create();

    $studentDataImport = new StudentDataImport();
    $studentDataImport->user()->associate($user);
    $studentDataImport->studentsImport()->associate($createImport($user, StudentImporter::class));
    $studentDataImport->enrollmentsImport()->associate($createImport($user, EnrollmentImporter::class));
    $studentDataImport->save();

    app(CreateTemporaryStudentDataImportTables::class)->execute($studentDataImport);

    DB::table("import_{$studentDataImport->studentsImport->getKey()}_students")->insert(['sisid' => 'S1']);
    DB::table("import_{$studentDataImport->enrollmentsImport->getKey()}_enrollments")->insert(['sisid' => 'S1', ...$enrollmentAttributes]);

    return $studentDataImport;
};

beforeEach(function (): void {
    Queue::fake();
});

describe('enrollments', function () use ($createStudentDataImportWithEnrollment): void {
    it('replaces the enrollments and keeps matching them to terms', function () use ($createStudentDataImportWithEnrollment): void {
        $term = Term::factory()->create();

        Enrollment::factory()->create(['sisid' => 'S0']);

        $studentDataImport = $createStudentDataImportWithEnrollment(['sis_term_id' => $term->sis_term_id]);

        app(CompleteStudentDataImport::class)->execute($studentDataImport);

        $enrollment = Enrollment::query()->sole();

        expect($enrollment->sisid)->toBe('S1')
            ->and($enrollment->term?->getKey())->toBe($term->getKey());
    });

    it('replaces the enrollments before the `enrollment_terms` view exists', function () use ($createStudentDataImportWithEnrollment): void {
        DB::statement('drop view "enrollment_terms"');

        $studentDataImport = $createStudentDataImportWithEnrollment([]);

        app(CompleteStudentDataImport::class)->execute($studentDataImport);

        expect(Enrollment::query()->sole()->sisid)->toBe('S1')
            ->and(DB::scalar("select to_regclass('enrollment_terms')"))->toBeNull();
    });
});
