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

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource\Pages\CreateBasicNeedProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource\Pages\EditBasicNeedProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource\Pages\ListBasicNeedPrograms;
use AdvisingApp\BasicNeeds\Models\BasicNeedProgram;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Actions\DeleteBulkAction;

it('can render list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedProgramResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');

    $this->get(BasicNeedProgramResource::getUrl('index'))->assertSuccessful();
});

it('can render data in list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('basic_need_program.view-any');

    $basicNeedPrograms = BasicNeedProgram::factory()->count(10)->create();

    livewire(ListBasicNeedPrograms::class)
        ->assertCanSeeTableRecords($basicNeedPrograms);
});

it('can render create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedProgramResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.create');

    $this->get(BasicNeedProgramResource::getUrl('create'))->assertSuccessful();
});

it('can validate input on create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedProgramResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.create');

    livewire(CreateBasicNeedProgram::class)
        ->fillForm([
            'name' => null,
            'basic_need_category_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'basic_need_category_id' => 'required']);
});

it('can create basic need program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.create');

    $newBasicNeedProgram = BasicNeedProgram::factory()->make();

    livewire(CreateBasicNeedProgram::class)
        ->fillForm([
            'name' => $newBasicNeedProgram->name,
            'description' => $newBasicNeedProgram->description,
            'basic_need_category_id' => $newBasicNeedProgram->basic_need_category_id,
            'contact_person' => $newBasicNeedProgram->contact_person,
            'contact_email' => $newBasicNeedProgram->contact_email,
            'contact_phone' => $newBasicNeedProgram->contact_phone,
            'location' => $newBasicNeedProgram->location,
            'availability' => $newBasicNeedProgram->availability,
            'eligibility_criteria' => $newBasicNeedProgram->eligibility_criteria,
            'application_process' => $newBasicNeedProgram->application_process,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(BasicNeedProgram::class, [
        'name' => $newBasicNeedProgram->name,
        'description' => $newBasicNeedProgram->description,
        'basic_need_category_id' => $newBasicNeedProgram->basic_need_category_id,
        'contact_person' => $newBasicNeedProgram->contact_person,
        'contact_email' => $newBasicNeedProgram->contact_email,
        'contact_phone' => $newBasicNeedProgram->contact_phone,
        'location' => $newBasicNeedProgram->location,
        'availability' => $newBasicNeedProgram->availability,
        'eligibility_criteria' => $newBasicNeedProgram->eligibility_criteria,
        'application_process' => $newBasicNeedProgram->application_process,
    ]);
});

it('can render edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedProgram = BasicNeedProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedProgramResource::getUrl('edit', [
            'record' => $basicNeedProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.*.update');

    $this->get(BasicNeedProgramResource::getUrl('edit', [
        'record' => $basicNeedProgram->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedProgram = BasicNeedProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedProgramResource::getUrl('edit', [
            'record' => $basicNeedProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.*.update');

    livewire(EditBasicNeedProgram::class, [
        'record' => $basicNeedProgram->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $basicNeedProgram->name,
            'description' => $basicNeedProgram->description,
            'basic_need_category_id' => $basicNeedProgram->basic_need_category_id,
            'contact_person' => $basicNeedProgram->contact_person,
            'contact_email' => $basicNeedProgram->contact_email,
            'contact_phone' => $basicNeedProgram->contact_phone,
            'location' => $basicNeedProgram->location,
            'availability' => $basicNeedProgram->availability,
            'eligibility_criteria' => $basicNeedProgram->eligibility_criteria,
            'application_process' => $basicNeedProgram->application_process,
        ]);
});

it('can validate input on edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedProgram = BasicNeedProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedProgramResource::getUrl('edit', [
            'record' => $basicNeedProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.*.update');

    livewire(EditBasicNeedProgram::class, [
        'record' => $basicNeedProgram->getRouteKey(),
    ])
        ->fillForm([
            'name' => null,
            'basic_need_category_id' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required', 'basic_need_category_id' => 'required']);
});

it('can save basic need program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $oldBasicNeedProgram = BasicNeedProgram::factory()->create();
    $newBasicNeedProgram = BasicNeedProgram::factory()->make();

    actingAs($user)->get(
        BasicNeedProgramResource::getUrl('edit', [
            'record' => $oldBasicNeedProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.*.update');

    livewire(EditBasicNeedProgram::class, [
        'record' => $oldBasicNeedProgram->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newBasicNeedProgram->name,
            'description' => $newBasicNeedProgram->description,
            'basic_need_category_id' => $newBasicNeedProgram->basic_need_category_id,
            'contact_person' => $newBasicNeedProgram->contact_person,
            'contact_email' => $newBasicNeedProgram->contact_email,
            'contact_phone' => $newBasicNeedProgram->contact_phone,
            'location' => $newBasicNeedProgram->location,
            'availability' => $newBasicNeedProgram->availability,
            'eligibility_criteria' => $newBasicNeedProgram->eligibility_criteria,
            'application_process' => $newBasicNeedProgram->application_process,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($oldBasicNeedProgram->refresh())
        ->name->toBe($newBasicNeedProgram->name)
        ->description->toBe($newBasicNeedProgram->description)
        ->basic_need_category_id->toBe($newBasicNeedProgram->basic_need_category_id)
        ->contact_person->toBe($newBasicNeedProgram->contact_person)
        ->contact_email->toBe($newBasicNeedProgram->contact_email)
        ->contact_phone->toBe($newBasicNeedProgram->contact_phone)
        ->location->toBe($newBasicNeedProgram->location)
        ->availability->toBe($newBasicNeedProgram->availability)
        ->eligibility_criteria->toBe($newBasicNeedProgram->eligibility_criteria)
        ->application_process->toBe($newBasicNeedProgram->application_process);
});

it('can render view page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedProgram = BasicNeedProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedProgramResource::getUrl('view', [
            'record' => $basicNeedProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.*.view');

    $this->get(BasicNeedProgramResource::getUrl('view', [
        'record' => $basicNeedProgram->getRouteKey(),
    ]))->assertSuccessful();
});

it('can delete basic need program', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedProgram = BasicNeedProgram::factory()->create();

    actingAs($user)->get(
        BasicNeedProgramResource::getUrl('edit', [
            'record' => $basicNeedProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.*.update');
    $user->givePermissionTo('basic_need_program.*.delete');

    livewire(EditBasicNeedProgram::class, [
        'record' => $basicNeedProgram->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);
    $this->assertSoftDeleted($basicNeedProgram);
});

it('can bulk delete basic need programs', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedPrograms = BasicNeedProgram::factory()->count(10)->create();

    actingAs($user)
        ->get(
            BasicNeedProgramResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_program.view-any');
    $user->givePermissionTo('basic_need_program.*.update');
    $user->givePermissionTo('basic_need_program.*.delete');
 
    livewire(ListBasicNeedPrograms::class)
        ->callTableBulkAction(DeleteBulkAction::class, $basicNeedPrograms);
 
    foreach ($basicNeedPrograms as $basicNeedProgram) {
        $this->assertSoftDeleted($basicNeedProgram);
    }
});