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
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageFormSubmissions;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function manageFormSubmissionsTestUser(): User
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineForms = true;
    $settings->save();

    return User::factory()->licensed(LicenseType::cases())->create();
}

test('archive action is visible when submission is not archived', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $submission = FormSubmission::factory()->create(['form_id' => $form->id]);

    livewire(ManageFormSubmissions::class, ['record' => $form->getRouteKey()])
        ->assertTableActionVisible('archive', $submission);
});

test('archive action successfully archives a submission', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $submission = FormSubmission::factory()->create(['form_id' => $form->id]);

    expect($submission->isArchived())->toBeFalse();

    livewire(ManageFormSubmissions::class, ['record' => $form->getRouteKey()])
        ->callTableAction('archive', $submission)
        ->assertNotified();

    assertDatabaseHas(FormSubmission::class, [
        'id' => $submission->id,
        'archived_at' => $submission->fresh()->archived_at,
    ]);

    expect($submission->fresh()->isArchived())->toBeTrue();
});

test('bulk archive action successfully archives multiple submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $submissions = FormSubmission::factory()->count(3)->create(['form_id' => $form->id]);

    $submissions->each(function (FormSubmission $submission): void {
        expect($submission->isArchived())->toBeFalse();
    });

    livewire(ManageFormSubmissions::class, ['record' => $form->getRouteKey()])
        ->callTableBulkAction('archive', $submissions)
        ->assertNotified();

    $submissions->each(function (FormSubmission $submission): void {
        expect($submission->fresh()->isArchived())->toBeTrue();
    });
});

test('archived submissions are hidden by default', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $activeSubmission = FormSubmission::factory()->create(['form_id' => $form->id]);
    $archivedSubmission = FormSubmission::factory()->create(['form_id' => $form->id, 'archived_at' => now()]);

    livewire(ManageFormSubmissions::class, ['record' => $form->getRouteKey()])
        ->assertCanSeeTableRecords([$activeSubmission])
        ->assertCanNotSeeTableRecords([$archivedSubmission]);
});

test('archived submissions are visible when the withoutArchived filter is removed', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $activeSubmission = FormSubmission::factory()->create(['form_id' => $form->id]);
    $archivedSubmission = FormSubmission::factory()->create(['form_id' => $form->id, 'archived_at' => now()]);

    livewire(ManageFormSubmissions::class, ['record' => $form->getRouteKey()])
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords([$activeSubmission, $archivedSubmission]);
});
