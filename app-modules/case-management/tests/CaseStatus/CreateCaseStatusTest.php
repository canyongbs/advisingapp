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

use AdvisingApp\CaseManagement\Filament\Resources\CaseStatusResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseStatusResource\Pages\CreateCaseStatus;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Tests\RequestFactories\CreateCaseStatusRequestFactory;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Tests\asSuperAdmin;

test('A successful action on the CreateCaseStatus page', function () {
    asSuperAdmin()
        ->get(
            CaseStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateCaseStatusRequestFactory::new()->create();

    livewire(CreateCaseStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseStatus::all());

    assertDatabaseHas(CaseStatus::class, $request);
});

test('CreateCaseStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CreateCaseStatus::class)
        ->fillForm(CreateCaseStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(CaseStatus::all());
})->with(
    [
        'name missing' => [CreateCaseStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateCaseStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateCaseStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateCaseStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('CreateCaseStatus is gated with proper access control', function () {
    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    actingAs($user)
        ->get(
            CaseStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateCaseStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)
        ->get(
            CaseStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseStatusRequestFactory::new()->create());

    livewire(CreateCaseStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseStatus::all());

    assertDatabaseHas(CaseStatus::class, $request->toArray());
});

test('CreateCaseStatus is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)
        ->get(
            CaseStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateCaseStatus::class)
        ->assertForbidden();

    $settings->data->addons->caseManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            CaseStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseStatusRequestFactory::new()->create());

    livewire(CreateCaseStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseStatus::all());

    assertDatabaseHas(CaseStatus::class, $request->toArray());
});
