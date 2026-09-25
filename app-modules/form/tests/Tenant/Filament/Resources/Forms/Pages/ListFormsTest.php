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
use AdvisingApp\Form\Models\FormEmailAutoReply;
use AdvisingApp\Form\Models\FormSubmission;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

it('does not allow duplicating a form to a name matching another non-archived form case-insensitively', function () {
    asSuperAdmin();

    Form::factory()->create(['name' => 'Existing Form']);
    $form = Form::factory()->create(['name' => 'Some Form']);

    livewire(ListForms::class)
        ->callTableAction('Duplicate', $form, data: ['name' => 'existing form'])
        ->assertHasTableActionErrors(['name' => 'unique']);
});

it('allows duplicating a form to a name freed up by an archived form', function () {
    asSuperAdmin();

    $archivedForm = Form::factory()->create(['name' => 'Reusable Name']);
    $archivedForm->archive();

    $form = Form::factory()->create(['name' => 'Some Form']);

    livewire(ListForms::class)
        ->callTableAction('Duplicate', $form, data: ['name' => 'reusable name'])
        ->assertHasNoTableActionErrors();

    expect(Form::query()->whereNull('archived_at')->where('name', 'reusable name')->exists())->toBeTrue();
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

it('archive bulk action archives all selected forms', function () {
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
    expect($formWithoutSubmissions->fresh()->archived_at)->not->toBeNull();
});

describe('duplication', function () {
    beforeEach(function () {
        asSuperAdmin();
    });

    it('gives a duplicated form its own version tree rather than sharing the original', function () {
        $form = Form::factory()->create();

        livewire(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();

        expect($duplicatedForm->root_id)->toBe($duplicatedForm->getKey())
            ->and($duplicatedForm->root_id)->not->toBe($form->root_id);
    });

    it('does not show the original form submissions count on the duplicated form', function () {
        $form = Form::factory()->create();

        FormSubmission::factory()->count(3)->create([
            'form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);

        livewire(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();

        livewire(ListForms::class)
            ->assertTableColumnStateSet('submissions_count', 3, record: $form)
            ->assertTableColumnStateSet('submissions_count', 0, record: $duplicatedForm);
    });

    it('copies the email auto reply to the duplicated form without adding one to the original', function () {
        $form = Form::factory()->create();

        $subject = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Thanks for submitting']]]]];
        $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'We received your submission.']]]]];

        $form->emailAutoReply()->updateOrCreate([], [
            'subject' => $subject,
            'body' => $body,
            'is_enabled' => true,
        ]);

        expect(FormEmailAutoReply::query()->where('form_id', $form->getKey())->count())->toBe(1);

        livewire(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();
        $duplicatedEmailAutoReply = $duplicatedForm->emailAutoReply;

        expect(FormEmailAutoReply::query()->where('form_id', $form->getKey())->count())->toBe(1)
            ->and(FormEmailAutoReply::query()->where('form_id', $duplicatedForm->getKey())->count())->toBe(1)
            ->and($duplicatedEmailAutoReply?->subject)->toEqual($subject)
            ->and($duplicatedEmailAutoReply?->body)->toEqual($body)
            ->and($duplicatedEmailAutoReply?->is_enabled)->toBeTrue();
    });

    it('copies the email auto reply images to the duplicated form', function () {
        Storage::fake('s3-public');

        $form = Form::factory()->create();

        $emailAutoReply = $form->emailAutoReply()->firstOrFail();
        $image = $emailAutoReply->addMedia(UploadedFile::fake()->image('logo.png'))->toMediaCollection('body', 's3-public');
        $emailAutoReply->update([
            'body' => ['type' => 'doc', 'content' => [['type' => 'image', 'attrs' => ['id' => $image->uuid]]]],
            'is_enabled' => true,
        ]);

        livewire(ListForms::class)
            ->callAction(TestAction::make('Duplicate')->table($form))
            ->assertHasNoFormErrors();

        $duplicatedForm = Form::query()->whereKeyNot($form->getKey())->firstOrFail();
        $duplicatedEmailAutoReply = $duplicatedForm->emailAutoReply()->firstOrFail();
        $duplicatedImage = $duplicatedEmailAutoReply->getFirstMedia('body');

        expect($duplicatedImage)->not->toBeNull()
            ->and($duplicatedImage->uuid)->not->toBe($image->uuid)
            ->and(json_encode($duplicatedEmailAutoReply->body))->toContain($duplicatedImage->uuid)->not->toContain($image->uuid)
            ->and($emailAutoReply->refresh()->getMedia('body')->pluck('uuid')->all())->toBe([$image->uuid])
            ->and(json_encode($emailAutoReply->body))->toContain($image->uuid);

        Storage::disk('s3-public')->assertExists($duplicatedImage->getPathRelativeToRoot());
    });
});
