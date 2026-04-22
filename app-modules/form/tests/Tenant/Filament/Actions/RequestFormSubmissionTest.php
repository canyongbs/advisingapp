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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AdvisingApp\Form\Actions\DeliverFormSubmissionRequestByEmail;
use AdvisingApp\Form\Actions\DeliverFormSubmissionRequestBySms;
use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\FormSubmissionsRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('requires form_id to submit the request', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => null,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
        ])
        ->assertHasTableActionErrors(['form_id' => 'required']);
});

it('can request a form submission via email', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
            'request_note' => 'Please fill out this form.',
        ])
        ->assertHasNoTableActionErrors();

    $submission = $student->formSubmissions()->first();

    expect($submission)->not->toBeNull()
        ->and($submission->form_id)->toBe($form->id)
        ->and($submission->request_method)->toBe(FormSubmissionRequestDeliveryMethod::Email)
        ->and($submission->request_note)->toBe('Please fill out this form.')
        ->and($submission->requester_id)->toBe($user->id)
        ->and($submission->submitted_at)->toBeNull()
        ->and($submission->canceled_at)->toBeNull();
});

it('can request a form submission via sms', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Sms->value,
            'request_note' => 'Please complete this survey.',
        ])
        ->assertHasNoTableActionErrors();

    $submission = $student->formSubmissions()->first();

    expect($submission)->not->toBeNull()
        ->and($submission->request_method)->toBe(FormSubmissionRequestDeliveryMethod::Sms);
});

it('defaults request_method to email', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
        ])
        ->assertHasNoTableActionErrors();

    $submission = $student->formSubmissions()->first();

    expect($submission)->not->toBeNull()
        ->and($submission->request_method)->toBe(FormSubmissionRequestDeliveryMethod::Email);
});

it('allows request_note to be optional', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
            'request_note' => null,
        ])
        ->assertHasNoTableActionErrors();

    $submission = $student->formSubmissions()->first();

    expect($submission)->not->toBeNull()
        ->and($submission->request_note)->toBeNull();
});

it('only lists forms with authentication enabled in the form select', function () {
    Queue::fake();

    asSuperAdmin();

    Student::factory()->create();
    $authenticatedForm = Form::factory()->create(['is_authenticated' => true]);
    $unauthenticatedForm = Form::factory()->create(['is_authenticated' => false]);

    $options = Form::query()
        ->where('is_authenticated', true)
        ->limit(50)
        ->pluck('name', 'id')
        ->all();

    expect($options)->toHaveKey($authenticatedForm->id)
        ->and($options)->not->toHaveKey($unauthenticatedForm->id);
});

it('reuses an existing requested submission for the same form instead of creating a new one', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    $existingSubmission = FormSubmission::factory()->create([
        'form_id' => $form->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => null,
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
        'request_note' => 'Old note',
    ]);
    $existingSubmission->requester()->associate($user);
    $existingSubmission->save();

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Sms->value,
            'request_note' => 'Updated note',
        ])
        ->assertHasNoTableActionErrors();

    expect($student->formSubmissions()->count())->toBe(1);

    $submission = $student->formSubmissions()->first();

    expect($submission->id)->toBe($existingSubmission->id)
        ->and($submission->request_method)->toBe(FormSubmissionRequestDeliveryMethod::Sms)
        ->and($submission->request_note)->toBe('Updated note');
});

it('creates a new submission when existing one is already submitted', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    FormSubmission::factory()->create([
        'form_id' => $form->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => now(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
        ])
        ->assertHasNoTableActionErrors();

    expect($student->formSubmissions()->count())->toBe(2);
});

it('creates a new submission when existing one is canceled', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    FormSubmission::factory()->create([
        'form_id' => $form->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => now(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
        ])
        ->assertHasNoTableActionErrors();

    expect($student->formSubmissions()->count())->toBe(2);
});

it('associates the current authenticated user as the requester', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
        ])
        ->assertHasNoTableActionErrors();

    $submission = $student->formSubmissions()->first();

    expect($submission->requester_id)->toBe($user->id)
        ->and($submission->requester->is($user))->toBeTrue();
});

it('dispatches the delivery job after creating the submission', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
        ])
        ->assertHasNoTableActionErrors();

    Queue::assertPushed(DeliverFormSubmissionRequestByEmail::class);
});

it('dispatches the sms delivery job when sms method is selected', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Sms->value,
        ])
        ->assertHasNoTableActionErrors();

    Queue::assertPushed(DeliverFormSubmissionRequestBySms::class);
});

it('sends a success notification after the request is sent', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $form = Form::factory()->create(['is_authenticated' => true]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('Request', data: [
            'form_id' => $form->id,
            'request_method' => FormSubmissionRequestDeliveryMethod::Email->value,
        ])
        ->assertHasNoTableActionErrors()
        ->assertNotified('Form request sent');
});
