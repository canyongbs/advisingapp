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

use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\StudentDataModel\Actions\CreateTemporaryStudentDataImportTables;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentImporter;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentDataImport;
use App\Enums\CareTeamRoleType;
use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

// The auto-subscription listener reads the real care_teams table, while the importer writes to a temporary one.
beforeEach(function (): void {
    Event::fake([TriggeredAutoSubscription::class]);
});

$importCareTeamRole = function (string $roleName): ?string {
    $user = User::factory()->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'import.csv';
    $import->file_path = 'imports/import.csv';
    $import->importer = StudentImporter::class;
    $import->total_rows = 1;
    $import->successful_rows = 1;
    $import->save();

    $studentDataImport = new StudentDataImport();
    $studentDataImport->user()->associate($user);
    $studentDataImport->studentsImport()->associate($import);
    $studentDataImport->save();

    app(CreateTemporaryStudentDataImportTables::class)->execute($studentDataImport);

    $student = Student::factory()->create();

    $importer = new StudentImporter($import, [], []);

    $data = new ReflectionProperty($importer, 'data');
    $data->setValue($importer, [
        'care_team_1' => $user->email,
        'care_team_role_1' => $roleName,
    ]);

    $record = new ReflectionProperty($importer, 'record');
    $record->setValue($importer, $student);

    (new ReflectionMethod($importer, 'afterCreate'))->invoke($importer);

    return DB::table("import_{$import->getKey()}_care_teams")->value('care_team_role_id');
};

it('assigns the student care team role matching the imported role name', function () use ($importCareTeamRole): void {
    $studentRole = CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Student]);
    CareTeamRole::factory()->create(['name' => 'advisor-other', 'type' => CareTeamRoleType::Prospect]);

    expect($importCareTeamRole('advisor'))->toBe($studentRole->getKey());
});

it('does not assign a prospect care team role to an imported student', function () use ($importCareTeamRole): void {
    $defaultStudentRole = CareTeamRole::factory()->create(['name' => 'Default', 'type' => CareTeamRoleType::Student, 'is_default' => true]);
    CareTeamRole::factory()->create(['name' => 'Recruiter', 'type' => CareTeamRoleType::Prospect]);

    expect($importCareTeamRole('recruiter'))->toBe($defaultStudentRole->getKey());
});

it('picks the student role when a student and a prospect role share a name', function () use ($importCareTeamRole): void {
    CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Prospect, 'created_at' => now()->subMinute()]);
    $studentRole = CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Student, 'created_at' => now()]);

    expect($importCareTeamRole('ADVISOR'))->toBe($studentRole->getKey());
});
