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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Jobs\SendApplicationNotificationJob;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Notifications\ApplicationSubmissionNotification;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

test('no notification sent when users exist but no channel is enabled', function () {
    Notification::fake();

    $user = User::factory()->create();
    $application = Application::factory()->create([
        'notify_via_app' => false,
        'notify_via_email' => false,
    ]);
    $application->notificationUsers()->attach($user);
    $submission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertNothingSent();
});

test('no notification sent when channels are enabled but no users qualify', function () {
    Notification::fake();

    $application = Application::factory()->create([
        'notify_to_care_team' => false,
        'notify_to_subscibers' => false,
        'notify_via_email' => true,
        'notify_via_app' => true,
    ]);
    $submission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertNothingSent();
});

test('notification sent to users via email only', function () {
    Notification::fake();

    $user = User::factory()->create();
    $application = Application::factory()->create([
        'notify_via_email' => true,
        'notify_via_app' => false,
    ]);
    $application->notificationUsers()->attach($user);
    $submission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTo(
        $user,
        ApplicationSubmissionNotification::class,
        fn ($notification, $channels) => $channels === ['mail']
    );
});

test('notification sent to users via app only', function () {
    Notification::fake();

    $user = User::factory()->create();
    $application = Application::factory()->create([
        'notify_via_email' => false,
        'notify_via_app' => true,
    ]);
    $application->notificationUsers()->attach($user);
    $submission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTo(
        $user,
        ApplicationSubmissionNotification::class,
        fn ($notification, $channels) => $channels === ['database']
    );
});

test('notification sent to users via both email and app', function () {
    Notification::fake();

    $user = User::factory()->create();
    $application = Application::factory()->create([
        'notify_via_email' => true,
        'notify_via_app' => true,
    ]);
    $application->notificationUsers()->attach($user);
    $submission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTo(
        $user,
        ApplicationSubmissionNotification::class,
        fn ($notification, $channels) => $channels === ['mail', 'database']
    );
});

test('care team members of a Student author are notified when notify_to_care_team is enabled', function () {
    Notification::fake();

    $careTeamUser = User::factory()->licensed(LicenseType::cases())->create();
    $careTeamRole = CareTeamRole::factory()->create();
    $student = Student::factory()->create();
    $student->careTeam()->attach($careTeamUser, ['care_team_role_id' => $careTeamRole->id]);

    $application = Application::factory()->create([
        'notify_to_care_team' => true,
        'notify_via_email' => true,
        'notify_via_app' => false,
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTo($careTeamUser, ApplicationSubmissionNotification::class);
});

test('care team members of a Prospect author are notified when notify_to_care_team is enabled', function () {
    Notification::fake();

    $careTeamUser = User::factory()->licensed(LicenseType::cases())->create();
    $careTeamRole = CareTeamRole::factory()->create();
    $prospect = Prospect::factory()->create();
    $prospect->careTeam()->attach($careTeamUser, ['care_team_role_id' => $careTeamRole->id]);

    $application = Application::factory()->create([
        'notify_to_care_team' => true,
        'notify_via_email' => true,
        'notify_via_app' => false,
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTo($careTeamUser, ApplicationSubmissionNotification::class);
});

test('subscribers of a Student author are notified when notify_to_subscibers is enabled', function () {
    Notification::fake();

    $subscriberUser = User::factory()->licensed(LicenseType::cases())->create();
    $student = Student::factory()->create();
    $subscriberUser->subscriptions()->create([
        'subscribable_id' => $student->getKey(),
        'subscribable_type' => $student->getMorphClass(),
    ]);

    $application = Application::factory()->create([
        'notify_to_subscibers' => true,
        'notify_via_email' => true,
        'notify_via_app' => false,
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTo($subscriberUser, ApplicationSubmissionNotification::class);
});

test('subscribers of a Prospect author are notified when notify_to_subscibers is enabled', function () {
    Notification::fake();

    $subscriberUser = User::factory()->licensed(LicenseType::cases())->create();
    $prospect = Prospect::factory()->create();
    $subscriberUser->subscriptions()->create([
        'subscribable_id' => $prospect->getKey(),
        'subscribable_type' => $prospect->getMorphClass(),
    ]);

    $application = Application::factory()->create([
        'notify_to_subscibers' => true,
        'notify_via_email' => true,
        'notify_via_app' => false,
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTo($subscriberUser, ApplicationSubmissionNotification::class);
});

test('subscribers are not notified when submission has no author', function () {
    Notification::fake();

    $application = Application::factory()->create([
        'notify_to_subscibers' => true,
        'notify_via_email' => true,
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => null,
        'author_id' => null,
    ]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertNothingSent();
});

test('a user appearing in all three recipient lists only receives one notification', function () {
    Notification::fake();

    $sharedUser = User::factory()->licensed(LicenseType::cases())->create();
    $careTeamRole = CareTeamRole::factory()->create();
    $student = Student::factory()->create();
    $student->careTeam()->attach($sharedUser, ['care_team_role_id' => $careTeamRole->id]);
    // $sharedUser->subscriptions()->create([
    //     'subscribable_id'   => $student->getKey(),
    //     'subscribable_type' => $student->getMorphClass(),
    // ]);

    $application = Application::factory()->create([
        'notify_to_care_team' => true,
        'notify_to_subscibers' => true,
        'notify_via_email' => true,
        'notify_via_app' => false,
    ]);
    $application->notificationUsers()->attach($sharedUser);
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTimes(ApplicationSubmissionNotification::class, 1);
    Notification::assertSentTo($sharedUser, ApplicationSubmissionNotification::class);
});

test('distinct users each receive their own notification without interference', function () {
    Notification::fake();

    $manualUser = User::factory()->create();
    $careTeamUser = User::factory()->licensed(LicenseType::cases())->create();
    $subscriberUser = User::factory()->licensed(LicenseType::cases())->create();

    $careTeamRole = CareTeamRole::factory()->create();
    $student = Student::factory()->create();
    $student->careTeam()->attach($careTeamUser, ['care_team_role_id' => $careTeamRole->id]);
    $subscriberUser->subscriptions()->create([
        'subscribable_id' => $student->getKey(),
        'subscribable_type' => $student->getMorphClass(),
    ]);

    $application = Application::factory()->create([
        'notify_to_care_team' => true,
        'notify_to_subscibers' => true,
        'notify_via_email' => true,
        'notify_via_app' => false,
    ]);
    $application->notificationUsers()->attach($manualUser);
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
    ]);

    (new SendApplicationNotificationJob($application, $submission))->handle();

    Notification::assertSentTimes(ApplicationSubmissionNotification::class, 3);
    Notification::assertSentTo($manualUser, ApplicationSubmissionNotification::class);
    Notification::assertSentTo($careTeamUser, ApplicationSubmissionNotification::class);
    Notification::assertSentTo($subscriberUser, ApplicationSubmissionNotification::class);
});
