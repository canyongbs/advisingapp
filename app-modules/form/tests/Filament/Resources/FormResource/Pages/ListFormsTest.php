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

use function Tests\asSuperAdmin;

use AdvisingApp\Form\Models\Form;

use function Pest\Livewire\livewire;

use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Filament\Resources\FormResource\Pages\ListForms;

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
