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

use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\EditForm;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;

use function Pest\Laravel\assertModelMissing;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows archive mode for the header archive action when the form has submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    livewire(EditForm::class, ['record' => $form->getRouteKey()])
        ->assertActionVisible('archive')
        ->assertActionHasLabel('archive', 'Archive');
});

it('shows delete mode for the header archive action when the form has no submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    livewire(EditForm::class, ['record' => $form->getRouteKey()])
        ->assertActionVisible('archive')
        ->assertActionHasLabel('archive', 'Delete');
});

it('archive action archives the form and redirects to the index when the form has submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    FormSubmission::factory()->create([
        'form_id' => $form->id,
        'submitted_at' => now(),
    ]);

    livewire(EditForm::class, ['record' => $form->getRouteKey()])
        ->callAction('archive')
        ->assertRedirect(FormResource::getUrl('index'));

    expect($form->fresh()->isArchived())->toBeTrue();
});

it('archive action deletes the form and redirects to the index when the form has no submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    livewire(EditForm::class, ['record' => $form->getRouteKey()])
        ->callAction('archive')
        ->assertRedirect(FormResource::getUrl('index'));

    assertModelMissing($form);
});
