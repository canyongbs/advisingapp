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
use AdvisingApp\Prospect\Models\ProspectCategory;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource\Pages\EditProspectCategory;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource\Pages\CreateProspectCategory;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource\Pages\ListProspectCategories;
use Filament\Tables\Actions\DeleteBulkAction;

it('can render list page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');

    $this->get(ProspectCategoryResource::getUrl('index'))->assertSuccessful();
});

it('can render data in list page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('prospect_category.view-any');

    $prospectCategories = ProspectCategory::factory()->count(10)->create();

    livewire(ListProspectCategories::class)
        ->assertCanSeeTableRecords($prospectCategories);
});

it('can render create page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectCategoryResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.create');

    $this->get(ProspectCategoryResource::getUrl('create'))->assertSuccessful();
});

it('can validate input on create page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectCategoryResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.create');

    livewire(CreateProspectCategory::class)
        ->fillForm([
            'name' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('can create prospect catgory', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.create');

    $newData = ProspectCategory::factory()->make();

    livewire(CreateProspectCategory::class)
        ->fillForm([
            'name' => $newData->name,
            'description' => $newData->description,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(ProspectCategory::class, [
        'name' => $newData->name,
        'description' => $newData->description,
    ]);
});

it('can render edit page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectCategory = ProspectCategory::factory()->create();

    actingAs($user)->get(
        ProspectCategoryResource::getUrl('edit', [
            'record' => $prospectCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.*.update');

    $this->get(ProspectCategoryResource::getUrl('edit', [
        'record' => $prospectCategory->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectCategory = ProspectCategory::factory()->create();

    actingAs($user)->get(
        ProspectCategoryResource::getUrl('edit', [
            'record' => $prospectCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.*.update');

    livewire(EditProspectCategory::class, [
        'record' => $prospectCategory->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectCategory->name,
            'description' => $prospectCategory->description,
        ]);
});

it('can validate input on edit page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectCategory = ProspectCategory::factory()->create();

    actingAs($user)->get(
        ProspectCategoryResource::getUrl('edit', [
            'record' => $prospectCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.*.update');

    livewire(EditProspectCategory::class, [
        'record' => $prospectCategory->getRouteKey(),
    ])
        ->fillForm([
            'name' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

it('can save prospect category', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $oldProspectCategory = ProspectCategory::factory()->create();
    $newProspectCategory = ProspectCategory::factory()->make();

    actingAs($user)->get(
        ProspectCategoryResource::getUrl('edit', [
            'record' => $oldProspectCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.*.update');

    livewire(EditProspectCategory::class, [
        'record' => $oldProspectCategory->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newProspectCategory->name,
            'description' => $newProspectCategory->description,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($oldProspectCategory->refresh())
        ->name->toBe($newProspectCategory->name)
        ->description->toBe($newProspectCategory->description);
});

it('can render view page', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectCategory = ProspectCategory::factory()->create();

    actingAs($user)->get(
        ProspectCategoryResource::getUrl('view', [
            'record' => $prospectCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.*.view');

    $this->get(ProspectCategoryResource::getUrl('view', [
        'record' => $prospectCategory->getRouteKey(),
    ]))->assertSuccessful();
});

it('can delete prospect category', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectCategory = ProspectCategory::factory()->create();

    actingAs($user)->get(
        ProspectCategoryResource::getUrl('edit', [
            'record' => $prospectCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.*.update');
    $user->givePermissionTo('prospect_category.*.delete');

    livewire(EditProspectCategory::class, [
        'record' => $prospectCategory->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);
    $this->assertSoftDeleted($prospectCategory);
});

it('can bulk delete prospect categories', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $prospectCategories = ProspectCategory::factory()->count(10)->create();

    actingAs($user)
        ->get(
            ProspectCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect_category.view-any');
    $user->givePermissionTo('prospect_category.*.update');
    $user->givePermissionTo('prospect_category.*.delete');
 
    livewire(ListProspectCategories::class)
        ->callTableBulkAction(DeleteBulkAction::class, $prospectCategories);
 
    foreach ($prospectCategories as $prospectCategory) {
        $this->assertSoftDeleted($prospectCategory);
    }
})->only();
