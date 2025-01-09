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

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Models\User;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ImportAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('renders the Import Programs Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Program::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('program.view-any');
    $user->givePermissionTo('program.create');

    actingAs($user);

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(ImportAction::class);

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('program.create');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(ImportAction::class);

    $user->givePermissionTo('program.create');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(ImportAction::class);
});

it('renders the Create Program Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Program::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('program.view-any');
    $user->givePermissionTo('program.create');

    actingAs($user);

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(CreateAction::class);

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('program.create');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(CreateAction::class);

    $user->givePermissionTo('program.create');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(CreateAction::class);
});

it('renders the Edit Program Table Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Program::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('program.view-any');
    $user->givePermissionTo('program.*.update');

    actingAs($user);

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(EditAction::class, $student->programs->first());

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('program.*.update');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(EditAction::class, $student->programs->first());

    $user->givePermissionTo('program.*.update');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(EditAction::class, $student->programs->first());
});

it('renders the Delete Program Table Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Program::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('program.view-any');
    $user->givePermissionTo('program.*.delete');

    actingAs($user);

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(DeleteAction::class, $student->programs->first());

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('program.*.delete');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionHidden(DeleteAction::class, $student->programs->first());

    $user->givePermissionTo('program.*.delete');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(DeleteAction::class, $student->programs->first());
});

it('renders the Delete Bulk Programs Table Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Program::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('program.view-any');
    $user->givePermissionTo('program.*.delete');

    actingAs($user);

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableBulkActionHidden(DeleteBulkAction::class, $student->programs->first());

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('program.*.delete');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableBulkActionHidden(DeleteBulkAction::class, $student->programs->first());

    $user->givePermissionTo('program.*.delete');

    livewire(ProgramsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableBulkActionVisible(DeleteBulkAction::class, $student->programs->first());
});
