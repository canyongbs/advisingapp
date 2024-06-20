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
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\BasicNeeds\Models\BasicNeedCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\Pages\EditBasicNeedCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\Pages\CreateBasicNeedCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\Pages\ListBasicNeedCategories;

it('can render list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');

    actingAs($user)->get(BasicNeedCategoryResource::getUrl('index'))
        ->assertSuccessful();
});

it('can render data in list page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('basic_need_category.view-any');

    $basicNeedCategories = BasicNeedCategory::factory()->count(10)->create();

    livewire(ListBasicNeedCategories::class)
        ->assertCanSeeTableRecords($basicNeedCategories);
});

it('can render create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedCategoryResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.create');

    actingAs($user)->get(BasicNeedCategoryResource::getUrl('create'))
        ->assertSuccessful();
});

it('can validate input on create page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user)
        ->get(
            BasicNeedCategoryResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.create');

    livewire(CreateBasicNeedCategory::class)
        ->fillForm([
            'name' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('can create basic need catgory', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.create');

    $newData = BasicNeedCategory::factory()->make();

    livewire(CreateBasicNeedCategory::class)
        ->fillForm([
            'name' => $newData->name,
            'description' => $newData->description,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(BasicNeedCategory::class, [
        'name' => $newData->name,
        'description' => $newData->description,
    ]);
});

it('can render edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedCategory = BasicNeedCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedCategoryResource::getUrl('edit', [
            'record' => $basicNeedCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.*.update');

    actingAs($user)->get(BasicNeedCategoryResource::getUrl('edit', [
        'record' => $basicNeedCategory->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedCategory = BasicNeedCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedCategoryResource::getUrl('edit', [
            'record' => $basicNeedCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.*.update');

    livewire(EditBasicNeedCategory::class, [
        'record' => $basicNeedCategory->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $basicNeedCategory->name,
            'description' => $basicNeedCategory->description,
        ]);
});

it('can validate input on edit page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedCategory = BasicNeedCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedCategoryResource::getUrl('edit', [
            'record' => $basicNeedCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.*.update');

    livewire(EditBasicNeedCategory::class, [
        'record' => $basicNeedCategory->getRouteKey(),
    ])
        ->fillForm([
            'name' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

it('can save basic need category', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $oldBasicNeedCategory = BasicNeedCategory::factory()->create();
    $newBasicNeedCategory = BasicNeedCategory::factory()->make();

    actingAs($user)->get(
        BasicNeedCategoryResource::getUrl('edit', [
            'record' => $oldBasicNeedCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.*.update');

    livewire(EditBasicNeedCategory::class, [
        'record' => $oldBasicNeedCategory->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newBasicNeedCategory->name,
            'description' => $newBasicNeedCategory->description,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($oldBasicNeedCategory->refresh())
        ->name->toBe($newBasicNeedCategory->name)
        ->description->toBe($newBasicNeedCategory->description);
});

it('can render view page', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedCategory = BasicNeedCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedCategoryResource::getUrl('view', [
            'record' => $basicNeedCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.*.view');

    actingAs($user)->get(BasicNeedCategoryResource::getUrl('view', [
        'record' => $basicNeedCategory->getRouteKey(),
    ]))->assertSuccessful();
});

it('can delete basic need category', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedCategory = BasicNeedCategory::factory()->create();

    actingAs($user)->get(
        BasicNeedCategoryResource::getUrl('edit', [
            'record' => $basicNeedCategory->getRouteKey(),
        ])
    )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.*.update');
    $user->givePermissionTo('basic_need_category.*.delete');

    livewire(EditBasicNeedCategory::class, [
        'record' => $basicNeedCategory->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    assertSoftDeleted($basicNeedCategory);
});

it('can bulk delete basic need categories', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $basicNeedCategories = BasicNeedCategory::factory()->count(10)->create();

    actingAs($user)
        ->get(
            BasicNeedCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('basic_need_category.view-any');
    $user->givePermissionTo('basic_need_category.*.update');
    $user->givePermissionTo('basic_need_category.*.delete');

    livewire(ListBasicNeedCategories::class)
        ->callTableBulkAction(DeleteBulkAction::class, $basicNeedCategories);

    foreach ($basicNeedCategories as $basicNeedCategory) {
        assertSoftDeleted($basicNeedCategory);
    }
});
