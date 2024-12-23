<?php

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can filter students by first generation', function () {
    Student::truncate();

    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $students = Student::factory()->count(5)->create();

    livewire(ListStudents::class)
        ->assertCanSeeTableRecords($students)
        ->filterTable('firstgen', true)
        ->assertCanSeeTableRecords($students->where('firstgen', true))
        ->assertCanNotSeeTableRecords($students->where('firstgen', false))
        ->filterTable('firstgen', false)
        ->assertCanSeeTableRecords($students->where('firstgen', false))
        ->assertCanNotSeeTableRecords($students->where('firstgen', true));
});
