<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CaseAssignment;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Tests\Tenant\Filament\Actions\RequestFactories\BulkCreateCaseActionRequestFactory;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows the form and validation', function (BulkCreateCaseActionRequestFactory $data, array $errors) {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = BulkCreateCaseActionRequestFactory::new($data)->without('assigned_to_id')->create();

    livewire(ListStudents::class)
        ->mountTableBulkAction('createCase', [$student->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasTableBulkActionErrors($errors);

    assertDatabaseMissing(CaseModel::class, $request);
})->with([
    'division_id required' => [
        BulkCreateCaseActionRequestFactory::new()->without('division_id'),
        ['division_id' => 'required'],
    ],
    'division_id exists' => [
        BulkCreateCaseActionRequestFactory::new()->state(['division_id' => (string) Str::uuid()]),
        ['division_id' => 'exists'],
    ],
    'status_id required' => [
        BulkCreateCaseActionRequestFactory::new()->without('status_id'),
        ['status_id' => 'required'],
    ],
    'status_id exists' => [
        BulkCreateCaseActionRequestFactory::new()->state(['status_id' => (string) Str::uuid()]),
        ['status_id' => 'exists'],
    ],
    'priority_id required' => [
        BulkCreateCaseActionRequestFactory::new()->without('priority_id'),
        ['priority_id' => 'required'],
    ],
    'priority_id exists' => [
        BulkCreateCaseActionRequestFactory::new()->state(['priority_id' => (string) Str::uuid()]),
        ['priority_id' => 'exists'],
    ],
]);

it('can successfully create bulk case with student', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = BulkCreateCaseActionRequestFactory::new()->create();

    livewire(ListStudents::class)
        ->mountTableBulkAction('createCase', [$student->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    $expected = [
        'division_id' => $request['division_id'],
        'status_id' => $request['status_id'],
        'respondent_type' => $student->getMorphClass(),
        'respondent_id' => $student->getKey(),
        'priority_id' => $request['priority_id'],
        'close_details' => $request['close_details'],
        'res_details' => $request['res_details'],
    ];

    assertDatabaseHas(CaseModel::class, $expected);

    $expectedassignments = [
        'case_model_id' => CaseModel::query()->where($expected)->first()->getKey(),
        'user_id' => $request['assigned_to_id'],
        'assigned_by_id' => auth()->user()->getKey(),
        'status' => CaseAssignmentStatus::Active,
    ];

    assertDatabaseHas(CaseAssignment::class, $expectedassignments);

    $request = BulkCreateCaseActionRequestFactory::new()->without('assigned_to_id')->create();

    livewire(ListStudents::class)
        ->mountTableBulkAction('createCase', [$student->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    $expected = [
        'division_id' => $request['division_id'],
        'status_id' => $request['status_id'],
        'respondent_type' => $student->getMorphClass(),
        'respondent_id' => $student->getKey(),
        'priority_id' => $request['priority_id'],
        'close_details' => $request['close_details'],
        'res_details' => $request['res_details'],
    ];

    assertDatabaseHas(CaseModel::class, $expected);

    $unexpectedassignments = [
        'case_model_id' => CaseModel::query()->where($expected)->first()->getKey(),
        'user_id' => null,
        'assigned_by_id' => auth()->user()->getKey(),
        'status' => CaseAssignmentStatus::Active,
    ];

    assertDatabaseMissing(CaseAssignment::class, $unexpectedassignments);
});

it('can successfully create bulk case with prospect', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();

    $request = BulkCreateCaseActionRequestFactory::new()->create();

    livewire(ListProspects::class)
        ->mountTableBulkAction('createCase', [$prospect->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    $expected = [
        'division_id' => $request['division_id'],
        'status_id' => $request['status_id'],
        'respondent_type' => $prospect->getMorphClass(),
        'respondent_id' => $prospect->getKey(),
        'priority_id' => $request['priority_id'],
        'close_details' => $request['close_details'],
        'res_details' => $request['res_details'],
    ];

    assertDatabaseHas(CaseModel::class, $expected);

    $expectedassignments = [
        'case_model_id' => CaseModel::query()->where($expected)->first()->getKey(),
        'user_id' => $request['assigned_to_id'],
        'assigned_by_id' => auth()->user()->getKey(),
        'status' => CaseAssignmentStatus::Active,
    ];

    assertDatabaseHas(CaseAssignment::class, $expectedassignments);

    $request = BulkCreateCaseActionRequestFactory::new()->without('assigned_to_id')->create();

    livewire(ListProspects::class)
        ->mountTableBulkAction('createCase', [$prospect->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    $expected = [
        'division_id' => $request['division_id'],
        'status_id' => $request['status_id'],
        'respondent_type' => $prospect->getMorphClass(),
        'respondent_id' => $prospect->getKey(),
        'priority_id' => $request['priority_id'],
        'close_details' => $request['close_details'],
        'res_details' => $request['res_details'],
    ];

    assertDatabaseHas(CaseModel::class, $expected);

    $unexpectedassignments = [
        'case_model_id' => CaseModel::query()->where($expected)->first()->getKey(),
        'user_id' => null,
        'assigned_by_id' => auth()->user()->getKey(),
        'status' => CaseAssignmentStatus::Active,
    ];

    assertDatabaseMissing(CaseAssignment::class, $unexpectedassignments);
});
