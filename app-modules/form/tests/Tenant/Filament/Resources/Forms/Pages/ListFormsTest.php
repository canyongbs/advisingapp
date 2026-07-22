<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ListForms;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Builder;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function listFormsTestUser(): User
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineForms = true;
    $settings->save();

    return User::factory()->licensed(LicenseType::cases())->create();
}

it('the create action is gated by the create permission', function () {
    $user = listFormsTestUser();
    $user->givePermissionTo('form.view-any');

    actingAs($user);

    livewire(ListForms::class)
        ->assertActionHidden('create');

    $user->givePermissionTo('form.create');

    livewire(ListForms::class)
        ->assertActionVisible('create');
});

it('the duplicate action is gated by the create permission', function () {
    $user = listFormsTestUser();
    $user->givePermissionTo('form.view-any');

    actingAs($user);

    $form = Form::factory()->create();

    livewire(ListForms::class)
        ->assertTableActionHidden('Duplicate', $form);

    $user->givePermissionTo('form.create');

    livewire(ListForms::class)
        ->assertTableActionVisible('Duplicate', $form);
});

it('can duplicate a form its steps and its fields', function () {
    asSuperAdmin();

    // Given that we have a form
    $form = Form::factory()->create();

    expect(Form::count())->toBe(1);

    // And we duplicate it
    livewire(ListForms::class)
        ->assertStatus(200)
        ->callTableAction('Duplicate', $form);

    // The form, along with all of its content, should be duplicated
    expect(Form::count())->toBe(2);

    $duplicatedForm = Form::where('id', '<>', $form->id)->first();

    expect($duplicatedForm->name)->toBe("Copy - {$form->name}");
    expect($duplicatedForm->fields->count())->toBe($form->fields->count());
    expect($duplicatedForm->steps->count())->toBe($form->steps->count());
});

it('will not duplicate form submissions if they exist', function () {
    asSuperAdmin();

    // Given that we have a form
    $form = Form::factory()->create();

    $submissionCount = $form->submissions()->count();

    // And we duplicate it
    livewire(ListForms::class)
        ->assertStatus(200)
        ->callTableAction('Duplicate', $form);

    // The form submissions should not be duplicated
    expect(FormSubmission::count())->toBe($submissionCount);

    $duplicatedForm = Form::where('id', '<>', $form->id)->first();

    expect($duplicatedForm->submissions()->count())->toBe(0);
});

it('displays the correct submissions count for a form', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(5)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    livewire(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 5, $form);
});

it('displays the correct submissions count across all versions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(3)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    $archivedVersion = Form::factory()->create([
        'root_id' => $form->root_id,
        'archived_at' => now(),
    ]);

    FormSubmission::factory()->count(4)->create([
        'form_id' => $archivedVersion->id,
        'submitted_at' => now(),
    ]);

    livewire(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 7, $form);
});

it('does not count submissions from unrelated forms in the submissions count', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(2)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    $unrelatedForm = Form::factory()->create();

    FormSubmission::factory()->count(10)->create([
        'form_id' => $unrelatedForm->id,
        'submitted_at' => now(),
    ]);

    livewire(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 2, $form);
});

it('does not count archived submissions in the submissions count', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->count(5)->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    $form->submissions()
        ->limit(2)
        ->update(['archived_at' => now()]);

    livewire(ListForms::class)
        ->assertTableColumnStateSet('submissions_count', 3, $form);
});

it('archives forms with submissions and deletes forms without submissions via the archive or delete bulk action', function () {
    asSuperAdmin();

    $formWithSubmissions = Form::factory()->create();

    FormSubmission::factory()->create([
        'form_id' => $formWithSubmissions->id,
        'submitted_at' => now(),
    ]);

    $formWithoutSubmissions = Form::factory()->create();

    $records = collect([$formWithSubmissions, $formWithoutSubmissions]);

    livewire(ListForms::class)
        ->selectTableRecords($records->pluck('id')->all())
        ->callAction(TestAction::make('archive')->table()->bulk())
        ->assertNotified();

    expect($formWithSubmissions->fresh()->archived_at)->not->toBeNull();
    expect(Form::find($formWithoutSubmissions->id))->toBeNull();
});

it('marks a form as used when any version has submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    $archivedVersion = Form::factory()->create([
        'root_id' => $form->root_id,
        'archived_at' => now(),
    ]);

    FormSubmission::factory()->create([
        'form_id' => $archivedVersion->id,
        'submitted_at' => now(),
    ]);

    expect($form->isUsed())->toBeTrue();
});

it('marks a form as unused when no version has submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    Form::factory()->create([
        'root_id' => $form->root_id,
        'archived_at' => now(),
    ]);

    expect($form->isUsed())->toBeFalse();
});

it('used query includes form roots that have submissions on any version', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    $archivedVersion = Form::factory()->create([
        'root_id' => $form->root_id,
        'archived_at' => now(),
    ]);

    FormSubmission::factory()->create([
        'form_id' => $archivedVersion->id,
        'submitted_at' => now(),
    ]);

    $unusedForm = Form::factory()->create();

    Form::factory()->create([
        'root_id' => $unusedForm->root_id,
        'archived_at' => now(),
    ]);

    $usedRootIds = Form::query()
        ->tap(fn (Builder $query) => $form->used($query))
        ->pluck('root_id')
        ->unique();

    expect($usedRootIds)->toContain($form->root_id)
        ->not->toContain($unusedForm->root_id);
});
