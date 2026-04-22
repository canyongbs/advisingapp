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

use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use AdvisingApp\Form\Enums\FormSubmissionStatus;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\FormSubmissionsRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('is hidden when the OnlineForms feature is disabled', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->onlineForms = false;
    $licenseSettings->save();

    asSuperAdmin();

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire(FormSubmissionsRelationManager::class);
});

it('is visible when the OnlineForms feature is enabled', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->onlineForms = true;
    $licenseSettings->save();

    asSuperAdmin();

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire(FormSubmissionsRelationManager::class);
});

it('can list form submissions for a student', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $submissions = FormSubmission::factory()->count(3)->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertCanSeeTableRecords($submissions);
});

it('does not show submissions belonging to other students', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    $otherStudent = Student::factory()->create();

    $ownSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    $otherSubmission = FormSubmission::factory()->create([
        'author_type' => $otherStudent->getMorphClass(),
        'author_id' => $otherStudent->getKey(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertCanSeeTableRecords([$ownSubmission])
        ->assertCanNotSeeTableRecords([$otherSubmission]);
});

it('has the expected table columns', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableColumnExists('submissible.name')
        ->assertTableColumnExists('status')
        ->assertTableColumnExists('submitted_at')
        ->assertTableColumnExists('requester.name')
        ->assertTableColumnExists('requested_at');
});

it('can search submissions by form name', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $matchingForm = Form::factory()->create(['name' => 'Enrollment Application']);
    $nonMatchingForm = Form::factory()->create(['name' => 'Exit Survey']);

    $matchingSubmission = FormSubmission::factory()->create([
        'form_id' => $matchingForm->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    $nonMatchingSubmission = FormSubmission::factory()->create([
        'form_id' => $nonMatchingForm->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->searchTable('Enrollment')
        ->assertCanSeeTableRecords([$matchingSubmission])
        ->assertCanNotSeeTableRecords([$nonMatchingSubmission]);
});

it('can sort submissions by submitted_at', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $olderSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => now()->subDays(5),
    ]);

    $newerSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => now(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->sortTable('submitted_at')
        ->assertCanSeeTableRecords([$olderSubmission, $newerSubmission], inOrder: true)
        ->sortTable('submitted_at', 'desc')
        ->assertCanSeeTableRecords([$newerSubmission, $olderSubmission], inOrder: true);
});

it('can filter submissions by status', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $requestedSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => null,
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);

    $submittedSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => now(),
    ]);

    $canceledSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => now(),
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->filterTable('status', FormSubmissionStatus::Requested->value)
        ->assertCanSeeTableRecords([$requestedSubmission])
        ->assertCanNotSeeTableRecords([$submittedSubmission, $canceledSubmission])
        ->filterTable('status', FormSubmissionStatus::Submitted->value)
        ->assertCanSeeTableRecords([$submittedSubmission])
        ->assertCanNotSeeTableRecords([$requestedSubmission, $canceledSubmission])
        ->filterTable('status', FormSubmissionStatus::Canceled->value)
        ->assertCanSeeTableRecords([$canceledSubmission])
        ->assertCanNotSeeTableRecords([$submittedSubmission, $requestedSubmission]);
});

it('shows the view action only for submitted submissions', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $submittedSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => now(),
    ]);

    $requestedSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => null,
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionVisible(ViewAction::class, $submittedSubmission)
        ->assertTableActionHidden(ViewAction::class, $requestedSubmission);
});

it('hides the view action for canceled submissions', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $canceledSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => now(),
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionHidden(ViewAction::class, $canceledSubmission);
});

it('can delete a form submission', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $submission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction(DeleteAction::class, $submission);

    expect($student->formSubmissions()->count())->toBe(0);
});

it('has the bulk delete action available', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableBulkActionExists(DeleteBulkAction::class);
});

it('can bulk delete form submissions', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $submissions = FormSubmission::factory()->count(3)->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableBulkAction(DeleteBulkAction::class, $submissions);

    expect($student->formSubmissions()->count())->toBe(0);
});

it('displays the requester name for submissions that have a requester', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    $requester = User::factory()->create(['name' => 'John Requester']);

    $submission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => null,
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);
    $submission->requester()->associate($requester);
    $submission->save();

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableColumnStateSet('requester.name', 'John Requester', $submission);
});

it('shows requested_at only when a requester is present', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    $requester = User::factory()->create();

    $requestedSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => null,
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);
    $requestedSubmission->requester()->associate($requester);
    $requestedSubmission->save();

    $directSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => now(),
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableColumnStateNotSet('requested_at', null, $requestedSubmission)
        ->assertTableColumnStateSet('requested_at', null, $directSubmission);
});

it('shows the correct status badge for each submission state', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $requestedSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => null,
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);

    $submittedSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => now(),
    ]);

    $canceledSubmission = FormSubmission::factory()->create([
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
        'canceled_at' => now(),
        'request_method' => FormSubmissionRequestDeliveryMethod::Email,
    ]);

    livewire(FormSubmissionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableColumnStateSet('status', FormSubmissionStatus::Requested, $requestedSubmission)
        ->assertTableColumnStateSet('status', FormSubmissionStatus::Submitted, $submittedSubmission)
        ->assertTableColumnStateSet('status', FormSubmissionStatus::Canceled, $canceledSubmission);
});
