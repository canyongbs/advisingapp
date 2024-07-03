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
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\ManageStudents;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\EditBasicNeedsProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\ListBasicNeedsPrograms;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\CreateBasicNeedsProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\StudentsRelationManager;

it('can render list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedsProgramResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');

    actingAs($user)->get(BasicNeedsProgramResource::getUrl('index'))
        ->assertSuccessful();
});

it('can render data in list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('basic_needs_program.view-any');

    $basicNeedsPrograms = BasicNeedsProgram::factory()->count(10)->create();

    livewire(ListBasicNeedsPrograms::class)
        ->assertCanSeeTableRecords($basicNeedsPrograms);
});

it('can render create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedsProgramResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.create');

    actingAs($user)->get(BasicNeedsProgramResource::getUrl('create'))
        ->assertSuccessful();
});

it('can validate input on create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedsProgramResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.create');

    livewire(CreateBasicNeedsProgram::class)
        ->fillForm([
            'name' => null,
            'basic_needs_category_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'basic_needs_category_id' => 'required']);
});

it('can create basic needs program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.create');

    $newBasicNeedsProgram = BasicNeedsProgram::factory()->make();

    livewire(CreateBasicNeedsProgram::class)
        ->fillForm([
            'name' => $newBasicNeedsProgram->name,
            'description' => $newBasicNeedsProgram->description,
            'basic_needs_category_id' => $newBasicNeedsProgram->basic_needs_category_id,
            'contact_person' => $newBasicNeedsProgram->contact_person,
            'contact_email' => $newBasicNeedsProgram->contact_email,
            'contact_phone' => $newBasicNeedsProgram->contact_phone,
            'location' => $newBasicNeedsProgram->location,
            'availability' => $newBasicNeedsProgram->availability,
            'eligibility_criteria' => $newBasicNeedsProgram->eligibility_criteria,
            'application_process' => $newBasicNeedsProgram->application_process,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(BasicNeedsProgram::class, [
        'name' => $newBasicNeedsProgram->name,
        'description' => $newBasicNeedsProgram->description,
        'basic_needs_category_id' => $newBasicNeedsProgram->basic_needs_category_id,
        'contact_person' => $newBasicNeedsProgram->contact_person,
        'contact_email' => $newBasicNeedsProgram->contact_email,
        'contact_phone' => $newBasicNeedsProgram->contact_phone,
        'location' => $newBasicNeedsProgram->location,
        'availability' => $newBasicNeedsProgram->availability,
        'eligibility_criteria' => $newBasicNeedsProgram->eligibility_criteria,
        'application_process' => $newBasicNeedsProgram->application_process,
    ]);
});

it('can render edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedsProgramResource::getUrl('edit', [
            'record' => $basicNeedsProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.*.update');

    actingAs($user)->get(BasicNeedsProgramResource::getUrl('edit', [
        'record' => $basicNeedsProgram->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedsProgramResource::getUrl('edit', [
            'record' => $basicNeedsProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.*.update');

    livewire(EditBasicNeedsProgram::class, [
        'record' => $basicNeedsProgram->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $basicNeedsProgram->name,
            'description' => $basicNeedsProgram->description,
            'basic_needs_category_id' => $basicNeedsProgram->basic_needs_category_id,
            'contact_person' => $basicNeedsProgram->contact_person,
            'contact_email' => $basicNeedsProgram->contact_email,
            'contact_phone' => $basicNeedsProgram->contact_phone,
            'location' => $basicNeedsProgram->location,
            'availability' => $basicNeedsProgram->availability,
            'eligibility_criteria' => $basicNeedsProgram->eligibility_criteria,
            'application_process' => $basicNeedsProgram->application_process,
        ]);
});

it('can validate input on edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedsProgramResource::getUrl('edit', [
            'record' => $basicNeedsProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.*.update');

    livewire(EditBasicNeedsProgram::class, [
        'record' => $basicNeedsProgram->getRouteKey(),
    ])
        ->fillForm([
            'name' => null,
            'basic_needs_category_id' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required', 'basic_needs_category_id' => 'required']);
});

it('can save basic needs program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $oldBasicNeedsProgram = BasicNeedsProgram::factory()->create();
    $newBasicNeedsProgram = BasicNeedsProgram::factory()->make();

    actingAs($user)->get(
        BasicNeedsProgramResource::getUrl('edit', [
            'record' => $oldBasicNeedsProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.*.update');

    livewire(EditBasicNeedsProgram::class, [
        'record' => $oldBasicNeedsProgram->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newBasicNeedsProgram->name,
            'description' => $newBasicNeedsProgram->description,
            'basic_needs_category_id' => $newBasicNeedsProgram->basic_needs_category_id,
            'contact_person' => $newBasicNeedsProgram->contact_person,
            'contact_email' => $newBasicNeedsProgram->contact_email,
            'contact_phone' => $newBasicNeedsProgram->contact_phone,
            'location' => $newBasicNeedsProgram->location,
            'availability' => $newBasicNeedsProgram->availability,
            'eligibility_criteria' => $newBasicNeedsProgram->eligibility_criteria,
            'application_process' => $newBasicNeedsProgram->application_process,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($oldBasicNeedsProgram->refresh())
        ->name->toBe($newBasicNeedsProgram->name)
        ->description->toBe($newBasicNeedsProgram->description)
        ->basic_needs_category_id->toBe($newBasicNeedsProgram->basic_needs_category_id)
        ->contact_person->toBe($newBasicNeedsProgram->contact_person)
        ->contact_email->toBe($newBasicNeedsProgram->contact_email)
        ->contact_phone->toBe($newBasicNeedsProgram->contact_phone)
        ->location->toBe($newBasicNeedsProgram->location)
        ->availability->toBe($newBasicNeedsProgram->availability)
        ->eligibility_criteria->toBe($newBasicNeedsProgram->eligibility_criteria)
        ->application_process->toBe($newBasicNeedsProgram->application_process);
});

it('can render view page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedsProgramResource::getUrl('view', [
            'record' => $basicNeedsProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.*.view');

    actingAs($user)->get(BasicNeedsProgramResource::getUrl('view', [
        'record' => $basicNeedsProgram->getRouteKey(),
    ]))->assertSuccessful();
});

it('can delete basic needs program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedsProgramResource::getUrl('edit', [
            'record' => $basicNeedsProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.*.update');
    $user->givePermissionTo('basic_needs_program.*.delete');

    livewire(EditBasicNeedsProgram::class, [
        'record' => $basicNeedsProgram->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    assertSoftDeleted($basicNeedsProgram);
});

it('can bulk delete basic needs programs', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsPrograms = BasicNeedsProgram::factory()->count(10)->create();

    actingAs($user)
        ->get(
            BasicNeedsProgramResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('basic_needs_program.*.update');
    $user->givePermissionTo('basic_needs_program.*.delete');

    livewire(ListBasicNeedsPrograms::class)
        ->callTableBulkAction(DeleteBulkAction::class, $basicNeedsPrograms);

    foreach ($basicNeedsPrograms as $basicNeedsProgram) {
        assertSoftDeleted($basicNeedsProgram);
    }
});

it('can filter basic needs program by `program category`', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsPrograms = BasicNeedsProgram::factory()->count(10)->create();
    $basic_needs_category_id = $basicNeedsPrograms->first()->basic_needs_category_id;

    actingAs($user)
        ->get(
            BasicNeedsProgramResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');

    livewire(ListBasicNeedsPrograms::class)
        ->assertCanSeeTableRecords($basicNeedsPrograms)
        ->filterTable('basic_category_id', $basic_needs_category_id)
        ->assertCanSeeTableRecords($basicNeedsPrograms->where('basic_needs_category_id', $basic_needs_category_id))
        ->assertCanNotSeeTableRecords($basicNeedsPrograms->where('basic_needs_category_id', '!=', $basic_needs_category_id));
});

it('can render manage student page for basic needs program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(BasicNeedsProgramResource::getUrl('students', [
            'record' => BasicNeedsProgram::factory()->create(),
        ]))->assertForbidden();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('student.view-any');

    actingAs($user)
        ->get(BasicNeedsProgramResource::getUrl('students', [
            'record' => BasicNeedsProgram::factory()->create(),
        ]))->assertSuccessful();
});

it('can attach a student to basic needs program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsProgram = BasicNeedsProgram::factory()->create();
    $student = Student::factory()->create();

    $user->givePermissionTo('basic_needs_program.view-any');
    $user->givePermissionTo('student.view-any');

    actingAs($user);

    livewire(StudentsRelationManager::class, [
        'ownerRecord' => $basicNeedsProgram,
        'pageClass' => ManageStudents::class,
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $student->getKey()]
        )->assertSuccessful();
});
