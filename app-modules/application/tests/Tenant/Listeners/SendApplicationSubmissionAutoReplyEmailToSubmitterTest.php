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

use AdvisingApp\Application\Actions\CreateApplicationVersion;
use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Notifications\ApplicationSubmissionAutoReplyNotification;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\AdmissionsAndEventsEmailAutoReplyFeature;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\seed;

beforeEach(function (): void {
    seed(ApplicationSubmissionStateSeeder::class);
});

$createApplicationWithEnabledEmailAutoReply = function (): Application {
    $application = Application::factory()->create();
    $application->emailAutoReply()->firstOrFail()->update(['is_enabled' => true]);

    return $application;
};

$submit = fn (Application $application, ?Student $author, array $attributes = []): ApplicationSubmission => ApplicationSubmission::factory()->create([
    'application_id' => $application->getKey(),
    'author_type' => $author?->getMorphClass(),
    'author_id' => $author?->getKey(),
    ...$attributes,
]);

it('sends the email auto reply to the author when an application is submitted', function () use ($createApplicationWithEnabledEmailAutoReply, $submit): void {
    $student = Student::factory()->create();
    $application = $createApplicationWithEnabledEmailAutoReply();

    Notification::fake();

    $submit($application, $student);

    Notification::assertSentToTimes($student, ApplicationSubmissionAutoReplyNotification::class, 1);
});

it('sends the email auto reply for a requested application only once it is submitted', function () use ($createApplicationWithEnabledEmailAutoReply, $submit): void {
    $student = Student::factory()->create();
    $application = $createApplicationWithEnabledEmailAutoReply();

    Notification::fake();

    $submission = $submit($application, $student, ['submitted_at' => null]);

    Notification::assertNotSentTo($student, ApplicationSubmissionAutoReplyNotification::class);

    $submission->update(['submitted_at' => now()]);

    Notification::assertSentToTimes($student, ApplicationSubmissionAutoReplyNotification::class, 1);
});

it('sends the email auto reply of the latest application version', function () use ($submit): void {
    $student = Student::factory()->create();
    $oldVersion = Application::factory()->create();

    Notification::fake();

    $submission = $submit($oldVersion, $student, ['submitted_at' => null]);

    $newVersion = app(CreateApplicationVersion::class)->execute($oldVersion->refresh(), ['name' => $oldVersion->name]);
    $newVersion->emailAutoReply()->firstOrFail()->update(['is_enabled' => true]);

    expect($oldVersion->fresh()->emailAutoReply)->toBeNull();

    $submission->update(['submitted_at' => now()]);

    Notification::assertSentToTimes($student, ApplicationSubmissionAutoReplyNotification::class, 1);
});

it('does not send the email auto reply when it is disabled', function () use ($submit): void {
    $student = Student::factory()->create();
    $application = Application::factory()->create();

    Notification::fake();

    $submit($application, $student);

    Notification::assertNotSentTo($student, ApplicationSubmissionAutoReplyNotification::class);
});

it('does not send the email auto reply when the submission has no author', function () use ($createApplicationWithEnabledEmailAutoReply, $submit): void {
    $application = $createApplicationWithEnabledEmailAutoReply();

    Notification::fake();

    $submit($application, null);

    Notification::assertNothingSent();
});

it('does not send the email auto reply when the author cannot receive email', function () use ($createApplicationWithEnabledEmailAutoReply, $submit): void {
    $student = Student::factory()->create();
    BouncedEmailAddress::factory()->create(['address' => $student->primaryEmailAddress?->address]);

    $application = $createApplicationWithEnabledEmailAutoReply();

    expect($student->canReceiveEmail())->toBeFalse();

    Notification::fake();

    $submit($application, $student);

    Notification::assertNotSentTo($student, ApplicationSubmissionAutoReplyNotification::class);
});

it('does not send the email auto reply when the feature is inactive', function () use ($createApplicationWithEnabledEmailAutoReply, $submit): void {
    $student = Student::factory()->create();
    $application = $createApplicationWithEnabledEmailAutoReply();

    AdmissionsAndEventsEmailAutoReplyFeature::deactivate();

    Notification::fake();

    $submit($application, $student);

    Notification::assertNotSentTo($student, ApplicationSubmissionAutoReplyNotification::class);
});
