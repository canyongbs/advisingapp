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
use AdvisingApp\Application\Jobs\SendApplicationSubmissionAutoReplyEmail;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Notifications\ApplicationSubmissionAutoReplyNotification;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\seed;

beforeEach(function (): void {
    seed(ApplicationSubmissionStateSeeder::class);

    // Run the job by hand in each test rather than through the submission event.
    Queue::fake([SendApplicationSubmissionAutoReplyEmail::class]);
});

$createSubmission = function (Application $application, ?Student $author): ApplicationSubmission {
    return ApplicationSubmission::factory()->create([
        'application_id' => $application->getKey(),
        'author_type' => $author?->getMorphClass(),
        'author_id' => $author?->getKey(),
    ]);
};

it('sends the email auto reply to the author when it is enabled', function () use ($createSubmission): void {
    Notification::fake();

    $student = Student::factory()->create();
    $application = Application::factory()->create();
    $application->emailAutoReply()->firstOrFail()->update(['is_enabled' => true]);

    $submission = $createSubmission($application, $student);

    Notification::assertNothingSent();

    (new SendApplicationSubmissionAutoReplyEmail($submission))->handle();

    Notification::assertSentToTimes($student, ApplicationSubmissionAutoReplyNotification::class, 1);
});

it('does not send the email auto reply when it is disabled', function () use ($createSubmission): void {
    Notification::fake();

    $student = Student::factory()->create();
    $application = Application::factory()->create();

    $submission = $createSubmission($application, $student);

    (new SendApplicationSubmissionAutoReplyEmail($submission))->handle();

    Notification::assertNotSentTo($student, ApplicationSubmissionAutoReplyNotification::class);
});

it('does not send the email auto reply when the submission has no author', function () use ($createSubmission): void {
    Notification::fake();

    $application = Application::factory()->create();
    $application->emailAutoReply()->firstOrFail()->update(['is_enabled' => true]);

    $submission = $createSubmission($application, null);

    (new SendApplicationSubmissionAutoReplyEmail($submission))->handle();

    Notification::assertNothingSent();
});

it('does not send the email auto reply when the author cannot receive email', function () use ($createSubmission): void {
    Notification::fake();

    $student = Student::factory()->create();
    BouncedEmailAddress::factory()->create(['address' => $student->primaryEmailAddress?->address]);

    $application = Application::factory()->create();
    $application->emailAutoReply()->firstOrFail()->update(['is_enabled' => true]);

    $submission = $createSubmission($application, $student);

    expect($student->canReceiveEmail())->toBeFalse();

    (new SendApplicationSubmissionAutoReplyEmail($submission))->handle();

    Notification::assertNotSentTo($student, ApplicationSubmissionAutoReplyNotification::class);
});

it('uses the email auto reply of the latest application version', function () use ($createSubmission): void {
    Notification::fake();

    $student = Student::factory()->create();
    $oldVersion = Application::factory()->create();
    $submission = $createSubmission($oldVersion, $student);

    $newVersion = app(CreateApplicationVersion::class)->execute($oldVersion->refresh(), ['name' => $oldVersion->name]);
    $newVersion->emailAutoReply()->firstOrFail()->update(['is_enabled' => true]);

    expect($oldVersion->fresh()->emailAutoReply)->toBeNull();

    (new SendApplicationSubmissionAutoReplyEmail($submission))->handle();

    Notification::assertSentToTimes($student, ApplicationSubmissionAutoReplyNotification::class, 1);
});
