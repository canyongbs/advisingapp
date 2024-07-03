<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Filament\Tables\Actions\AttachAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManagePrograms;
use AdvisingApp\StudentDataModel\Filament\Resources\BasicNeedsProgramResource\RelationManagers\BasicNeedsProgramsRelationManager;

it('can render manage basic needs program for student', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(StudentResource::getUrl('programs', [
            'record' => Student::factory()->create(),
        ]))->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('student.view-any');

    actingAs($user)
        ->get(StudentResource::getUrl('programs', [
            'record' => Student::factory()->create(),
        ]))->assertSuccessful();
});

it('can attach a basic needs program to a student', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();
    $student = Student::factory()->create();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('student.view-any');

    actingAs($user);

    livewire(BasicNeedsProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ManagePrograms::class,
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $basicNeedsProgram->getKey()]
        )->assertSuccessful();
});
