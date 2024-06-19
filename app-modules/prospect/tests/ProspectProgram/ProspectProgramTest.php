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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Filament\Resources\ProspectProgramResource;
use AdvisingApp\Prospect\Filament\Resources\ProspectProgramResource\Pages\CreateProspectProgram;
use AdvisingApp\Prospect\Filament\Resources\ProspectProgramResource\Pages\EditProspectProgram;
use AdvisingApp\Prospect\Filament\Resources\ProspectProgramResource\Pages\ListProspectPrograms;
use AdvisingApp\Prospect\Models\ProspectProgram;
use Filament\Tables\Actions\DeleteBulkAction;

it('can render list page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectProgramResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');

    $this->get(ProspectProgramResource::getUrl('index'))->assertSuccessful();
});

it('can render data in list page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('prospect_program.view-any');

    $prospectPrograms = ProspectProgram::factory()->count(10)->create();

    livewire(ListProspectPrograms::class)
        ->assertCanSeeTableRecords($prospectPrograms);
});

it('can render create page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectProgramResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.create');

    $this->get(ProspectProgramResource::getUrl('create'))->assertSuccessful();
});

it('can validate input on create page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectProgramResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.create');

    livewire(CreateProspectProgram::class)
        ->fillForm([
            'name' => null,
            'prospect_category_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'prospect_category_id' => 'required']);
});

it('can create prospect program', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.create');

    $newProspectProgram = ProspectProgram::factory()->make();

    livewire(CreateProspectProgram::class)
        ->fillForm([
            'name' => $newProspectProgram->name,
            'description' => $newProspectProgram->description,
            'prospect_category_id' => $newProspectProgram->prospect_category_id,
            'contact_person' => $newProspectProgram->contact_person,
            'contact_email' => $newProspectProgram->contact_email,
            'contact_phone' => $newProspectProgram->contact_phone,
            'location' => $newProspectProgram->location,
            'availability' => $newProspectProgram->availability,
            'eligibility_criteria' => $newProspectProgram->eligibility_criteria,
            'application_process' => $newProspectProgram->application_process,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(ProspectProgram::class, [
        'name' => $newProspectProgram->name,
        'description' => $newProspectProgram->description,
        'prospect_category_id' => $newProspectProgram->prospect_category_id,
        'contact_person' => $newProspectProgram->contact_person,
        'contact_email' => $newProspectProgram->contact_email,
        'contact_phone' => $newProspectProgram->contact_phone,
        'location' => $newProspectProgram->location,
        'availability' => $newProspectProgram->availability,
        'eligibility_criteria' => $newProspectProgram->eligibility_criteria,
        'application_process' => $newProspectProgram->application_process,
    ]);
});

it('can render edit page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectProgram = ProspectProgram::factory()->create();

    actingAs($user)->get(
        ProspectProgramResource::getUrl('edit', [
            'record' => $prospectProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.*.update');

    $this->get(ProspectProgramResource::getUrl('edit', [
        'record' => $prospectProgram->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectProgram = ProspectProgram::factory()->create();

    actingAs($user)->get(
        ProspectProgramResource::getUrl('edit', [
            'record' => $prospectProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.*.update');

    livewire(EditProspectProgram::class, [
        'record' => $prospectProgram->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectProgram->name,
            'description' => $prospectProgram->description,
            'prospect_category_id' => $prospectProgram->prospect_category_id,
            'contact_person' => $prospectProgram->contact_person,
            'contact_email' => $prospectProgram->contact_email,
            'contact_phone' => $prospectProgram->contact_phone,
            'location' => $prospectProgram->location,
            'availability' => $prospectProgram->availability,
            'eligibility_criteria' => $prospectProgram->eligibility_criteria,
            'application_process' => $prospectProgram->application_process,
        ]);
});

it('can validate input on edit page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectProgram = ProspectProgram::factory()->create();

    actingAs($user)->get(
        ProspectProgramResource::getUrl('edit', [
            'record' => $prospectProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.*.update');

    livewire(EditProspectProgram::class, [
        'record' => $prospectProgram->getRouteKey(),
    ])
        ->fillForm([
            'name' => null,
            'prospect_category_id' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required', 'prospect_category_id' => 'required']);
});

it('can save prospect program', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $oldProspectProgram = ProspectProgram::factory()->create();
    $newProspectProgram = ProspectProgram::factory()->make();

    actingAs($user)->get(
        ProspectProgramResource::getUrl('edit', [
            'record' => $oldProspectProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.*.update');

    livewire(EditProspectProgram::class, [
        'record' => $oldProspectProgram->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newProspectProgram->name,
            'description' => $newProspectProgram->description,
            'prospect_category_id' => $newProspectProgram->prospect_category_id,
            'contact_person' => $newProspectProgram->contact_person,
            'contact_email' => $newProspectProgram->contact_email,
            'contact_phone' => $newProspectProgram->contact_phone,
            'location' => $newProspectProgram->location,
            'availability' => $newProspectProgram->availability,
            'eligibility_criteria' => $newProspectProgram->eligibility_criteria,
            'application_process' => $newProspectProgram->application_process,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($oldProspectProgram->refresh())
        ->name->toBe($newProspectProgram->name)
        ->description->toBe($newProspectProgram->description)
        ->prospect_category_id->toBe($newProspectProgram->prospect_category_id)
        ->contact_person->toBe($newProspectProgram->contact_person)
        ->contact_email->toBe($newProspectProgram->contact_email)
        ->contact_phone->toBe($newProspectProgram->contact_phone)
        ->location->toBe($newProspectProgram->location)
        ->availability->toBe($newProspectProgram->availability)
        ->eligibility_criteria->toBe($newProspectProgram->eligibility_criteria)
        ->application_process->toBe($newProspectProgram->application_process);
});

it('can render view page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectProgram = ProspectProgram::factory()->create();

    actingAs($user)->get(
        ProspectProgramResource::getUrl('view', [
            'record' => $prospectProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.*.view');

    $this->get(ProspectProgramResource::getUrl('view', [
        'record' => $prospectProgram->getRouteKey(),
    ]))->assertSuccessful();
});

it('can delete prospect program', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectProgram = ProspectProgram::factory()->create();

    actingAs($user)->get(
        ProspectProgramResource::getUrl('edit', [
            'record' => $prospectProgram->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.*.update');
    $user->givePermissionTo('prospect_program.*.delete');

    livewire(EditProspectProgram::class, [
        'record' => $prospectProgram->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);
    $this->assertSoftDeleted($prospectProgram);
});

it('can bulk delete prospect programs', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectPrograms = ProspectProgram::factory()->count(10)->create();

    actingAs($user)
        ->get(
            ProspectProgramResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect_program.view-any');
    $user->givePermissionTo('prospect_program.*.update');
    $user->givePermissionTo('prospect_program.*.delete');
 
    livewire(ListProspectPrograms::class)
        ->callTableBulkAction(DeleteBulkAction::class, $prospectPrograms);
 
    foreach ($prospectPrograms as $prospectProgram) {
        $this->assertSoftDeleted($prospectProgram);
    }
});