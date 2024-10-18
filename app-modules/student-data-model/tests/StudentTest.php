<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Filament\Tables\Actions\AttachAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentPrograms;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\ProgramRelationManager;

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
})->skip();

it('can attach a basic needs program to a student', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();
    $student = Student::factory()->create();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('student.view-any');

    actingAs($user);

    livewire(ProgramRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ManageStudentPrograms::class,
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $basicNeedsProgram->getKey()]
        )->assertSuccessful();
});
