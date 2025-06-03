<?php

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Tests\Tenant\Filament\Actions\RequestFactories\BulkCreateCaseActionRequestFactory;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows the form and validation', function (BulkCreateCaseActionRequestFactory $data, array $errors) {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = collect(BulkCreateCaseActionRequestFactory::new($data)->create());

    livewire(ListStudents::class)
        ->mountTableBulkAction('createCase', [$student->getKey()])
        ->setTableBulkActionData($request->toArray())
        ->callMountedTableBulkAction()
        ->assertHasTableBulkActionErrors($errors);

    assertDatabaseMissing(CaseModel::class, $request->toArray());
})->with([
    'division_id required' => [
        BulkCreateCaseActionRequestFactory::new()->without('division_id'),
        ['division_id' => 'required'],
    ],
    'status_id required' => [
        BulkCreateCaseActionRequestFactory::new()->without('status_id'),
        ['status_id' => 'required'],
    ],
    'priority_id required' => [
        BulkCreateCaseActionRequestFactory::new()->without('priority_id'),
        ['priority_id' => 'required'],
    ],
]);

it('can successfully create bulk case with student', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = collect(BulkCreateCaseActionRequestFactory::new()->create([
        'respondent_id' => $student->getKey(),
        'respondent_type' => $student->getMorphClass(),
    ]));

    livewire(ListStudents::class)
        ->mountTableBulkAction('createCase', [$student->getKey()])
        ->setTableBulkActionData([
            ...$request,
        ])
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    assertDatabaseHas(CaseModel::class, $request->toArray());
});

it('can successfully create bulk case with prospect', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();

    $request = collect(BulkCreateCaseActionRequestFactory::new()->create([
        'respondent_id' => $prospect->getKey(),
        'respondent_type' => $prospect->getMorphClass(),
    ]));

    livewire(ListProspects::class)
        ->mountTableBulkAction('createCase', [$prospect->getKey()])
        ->setTableBulkActionData($request->toArray())
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    assertDatabaseHas(CaseModel::class, $request->toArray());
});
