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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Importers\StudentGroupSubjectImporter;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Import;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;

use function Tests\asSuperAdmin;

function resolveStudentGroupSubject(Group $group, string $state): ?Student
{
    $importer = new class (
        import: Import::create([
            'file_name' => 'students.csv',
            'file_path' => 'students.csv',
            'importer' => StudentGroupSubjectImporter::class,
            'processed_rows' => 0,
            'total_rows' => 1,
            'successful_rows' => 0,
            'user_id' => User::factory()->create()->getKey(),
        ]),
        columnMap: ['subject' => 'subject'],
        options: ['segment_id' => $group->getKey()],
    ) extends StudentGroupSubjectImporter {
        public function primeRecord(): void
        {
            $this->record = $this->resolveRecord();
        }
    };

    $importer->primeRecord();

    $subject = collect($importer->getCachedColumns())
        ->firstOrFail(fn (ImportColumn $column): bool => $column->getName() === 'subject')
        ->resolveRelatedRecord($state);

    assert($subject === null || $subject instanceof Student);

    return $subject;
}

it('resolves a student by their SIS ID', function () {
    asSuperAdmin();

    $group = Group::factory()->create(['model' => GroupModel::Student]);
    $student = Student::factory()->create();

    expect(resolveStudentGroupSubject($group, $student->sisid)?->getKey())->toBe($student->getKey());
});

it('resolves a student by their other ID', function () {
    asSuperAdmin();

    $group = Group::factory()->create(['model' => GroupModel::Student]);
    $student = Student::factory()->create(['otherid' => 'OTHER-1']);

    expect(resolveStudentGroupSubject($group, 'OTHER-1')?->getKey())->toBe($student->getKey());
});

it('does not resolve an archived student by their SIS ID', function () {
    asSuperAdmin();

    $group = Group::factory()->create(['model' => GroupModel::Student]);
    $student = Student::factory()->create();
    $student->archive();

    expect(resolveStudentGroupSubject($group, $student->sisid))->toBeNull();
});

it('does not resolve an archived student by their other ID', function () {
    asSuperAdmin();

    $group = Group::factory()->create(['model' => GroupModel::Student]);
    $student = Student::factory()->create(['otherid' => 'OTHER-2']);
    $student->archive();

    expect(resolveStudentGroupSubject($group, 'OTHER-2'))->toBeNull();
});
