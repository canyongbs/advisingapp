<?php

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can filter by name search', function (): void {
    asSuperAdmin();

    $regularEnrollment = Enrollment::factory()->state([
        'name' => 'Regular Course Name',
    ]);

    $searchableEnrollment = Enrollment::factory()->state([
        'name' => 'Unique Course Name',
    ]);

    $student = Student::factory()
        ->has($regularEnrollment, 'enrollments')
        ->has($searchableEnrollment, 'enrollments')
        ->create();

    livewire(EnrollmentsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertCanSeeTableRecords($student->enrollments)
        ->searchTable('Unique')
        ->assertCanSeeTableRecords([$student->enrollments->where('name', 'Unique Course Name')->first()])
        ->assertCanNotSeeTableRecords($student->enrollments->where('name', 'Regular Course Name'));
})->only();
