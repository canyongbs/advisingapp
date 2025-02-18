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

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages\CreateBasicNeedsCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages\EditBasicNeedsCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages\ListBasicNeedsCategories;
use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

it('can render list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    actingAs($user)
        ->get(
            BasicNeedsCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');

    actingAs($user)->get(BasicNeedsCategoryResource::getUrl('index'))
        ->assertSuccessful();
});

it('can render data in list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');

    $basicNeedsCategories = BasicNeedsCategory::factory()->count(10)->create();

    livewire(ListBasicNeedsCategories::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($basicNeedsCategories);
});

it('can render create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedsCategoryResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)->get(BasicNeedsCategoryResource::getUrl('create'))
        ->assertSuccessful();
});

it('can validate input on create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedsCategoryResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    livewire(CreateBasicNeedsCategory::class)
        ->fillForm([
            'name' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('can create basic needs catgory', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    $basicNeedsCategory = BasicNeedsCategory::factory()->make();

    livewire(CreateBasicNeedsCategory::class)
        ->fillForm([
            'name' => $basicNeedsCategory->name,
            'description' => $basicNeedsCategory->description,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(BasicNeedsCategory::class, [
        'name' => $basicNeedsCategory->name,
        'description' => $basicNeedsCategory->description,
    ]);
});

it('can render edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsCategory = BasicNeedsCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedsCategoryResource::getUrl('edit', [
            'record' => $basicNeedsCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)->get(BasicNeedsCategoryResource::getUrl('edit', [
        'record' => $basicNeedsCategory->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsCategory = BasicNeedsCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedsCategoryResource::getUrl('edit', [
            'record' => $basicNeedsCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    livewire(EditBasicNeedsCategory::class, [
        'record' => $basicNeedsCategory->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $basicNeedsCategory->name,
            'description' => $basicNeedsCategory->description,
        ]);
});

it('can validate input on edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsCategory = BasicNeedsCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedsCategoryResource::getUrl('edit', [
            'record' => $basicNeedsCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    livewire(EditBasicNeedsCategory::class, [
        'record' => $basicNeedsCategory->getRouteKey(),
    ])
        ->fillForm([
            'name' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

it('can save basic needs category', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $oldBasicNeedsCategory = BasicNeedsCategory::factory()->create();
    $newBasicNeedsCategory = BasicNeedsCategory::factory()->make();

    actingAs($user)->get(
        BasicNeedsCategoryResource::getUrl('edit', [
            'record' => $oldBasicNeedsCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    livewire(EditBasicNeedsCategory::class, [
        'record' => $oldBasicNeedsCategory->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newBasicNeedsCategory->name,
            'description' => $newBasicNeedsCategory->description,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($oldBasicNeedsCategory->refresh())
        ->name->toBe($newBasicNeedsCategory->name)
        ->description->toBe($newBasicNeedsCategory->description);
});

it('can render view page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsCategory = BasicNeedsCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedsCategoryResource::getUrl('view', [
            'record' => $basicNeedsCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.view');

    actingAs($user)->get(BasicNeedsCategoryResource::getUrl('view', [
        'record' => $basicNeedsCategory->getRouteKey(),
    ]))->assertSuccessful();
});

it('can delete basic needs category', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsCategory = BasicNeedsCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedsCategoryResource::getUrl('edit', [
            'record' => $basicNeedsCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');
    $user->givePermissionTo('product_admin.*.delete');

    livewire(EditBasicNeedsCategory::class, [
        'record' => $basicNeedsCategory->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    assertSoftDeleted($basicNeedsCategory);
});

it('can bulk delete basic needs categories', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedsCategories = BasicNeedsCategory::factory()->count(10)->create();

    actingAs($user)
        ->get(
            BasicNeedsCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');
    $user->givePermissionTo('product_admin.*.delete');

    livewire(ListBasicNeedsCategories::class)
        ->set('tableRecordsPerPage', 10)
        ->callTableBulkAction(DeleteBulkAction::class, $basicNeedsCategories);

    foreach ($basicNeedsCategories as $basicNeedsCategory) {
        assertSoftDeleted($basicNeedsCategory);
    }
});
