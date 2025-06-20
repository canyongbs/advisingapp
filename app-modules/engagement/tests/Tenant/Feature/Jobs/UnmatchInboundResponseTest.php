<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Jobs\UnmatchedInboundCommunicationsJob;
use AdvisingApp\Engagement\Models\UnmatchedInboundCommunication;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;

use function Pest\Laravel\assertDatabaseHas;

it('can check student email in unmatched inbound communications if exist add engagement to the student', function () {
    $unmatchRecordOne = UnmatchedInboundCommunication::factory()->create([
        'sender' => 'example@example.com',
        'type' => EngagementResponseType::Email,
    ]);
    UnmatchedInboundCommunication::factory()->create([
        'sender' => 'notmatch@example.com',
        'type' => EngagementResponseType::Email,
    ]);

    $student = Student::factory()->create();

    StudentEmailAddress::factory()->for($student, 'student')->create([
        'address' => 'example@example.com',
    ]);

    expect(UnmatchedInboundCommunication::count())->toBe(2);
    expect(Student::query()->whereHas('engagementResponses')->count())->toBe(0);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => 'example@example.com',
    ]);

    $job = new UnmatchedInboundCommunicationsJob();
    $job->handle();

    expect(UnmatchedInboundCommunication::count())->toBe(1);
    expect(Student::query()->whereHas('engagementResponses')->count())->toBe(1);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => 'notmatch@example.com',
    ]);
    assertDatabaseHas('engagement_responses', [
        'type' => EngagementResponseType::Email,
        'content' => $unmatchRecordOne->body,
        'subject' => $unmatchRecordOne->subject,
        'sender_id' => $student->sisid,
        'status' => EngagementResponseStatus::New,
    ]);
});

it('can check prospect email in unmatched inbound communications if exist add engagement to the prospect', function () {
    $unmatchRecordOne = UnmatchedInboundCommunication::factory()->create([
        'sender' => 'prospect@example.com',
        'type' => EngagementResponseType::Email,
    ]);
    UnmatchedInboundCommunication::factory()->create([
        'sender' => 'notmatch@example.com',
        'type' => EngagementResponseType::Email,
    ]);

    $prospect = Prospect::factory()->create();

    ProspectEmailAddress::factory()->for($prospect, 'prospect')->create([
        'address' => 'prospect@example.com',
    ]);

    expect(UnmatchedInboundCommunication::count())->toBe(2);
    expect(Prospect::query()->whereHas('engagementResponses')->count())->toBe(0);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => 'prospect@example.com',
    ]);

    $job = new UnmatchedInboundCommunicationsJob();
    $job->handle();

    expect(UnmatchedInboundCommunication::count())->toBe(1);
    expect(Prospect::query()->whereHas('engagementResponses')->count())->toBe(1);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => 'notmatch@example.com',
    ]);
    assertDatabaseHas('engagement_responses', [
        'type' => EngagementResponseType::Email,
        'content' => $unmatchRecordOne->body,
        'subject' => $unmatchRecordOne->subject,
        'sender_id' => $prospect->getKey(),
        'status' => EngagementResponseStatus::New,
    ]);
});
it('can check student phone in unmatched inbound communications if exist add engagement to the student', function () {
    $unmatchRecordOne = UnmatchedInboundCommunication::factory()->create([
        'sender' => '1234567890',
        'type' => EngagementResponseType::Sms,
    ]);
    UnmatchedInboundCommunication::factory()->create([
        'sender' => '0987654321',
        'type' => EngagementResponseType::Sms,
    ]);

    $student = Student::factory()->create();

    StudentPhoneNumber::factory()->for($student, 'student')->create([
        'number' => '1234567890',
    ]);

    expect(UnmatchedInboundCommunication::count())->toBe(2);
    expect(Student::query()->whereHas('engagementResponses')->count())->toBe(0);
    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => '1234567890',
    ]);

    $job = new UnmatchedInboundCommunicationsJob();
    $job->handle();

    expect(UnmatchedInboundCommunication::count())->toBe(1);
    expect(Student::query()->whereHas('engagementResponses')->count())->toBe(1);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => '0987654321',
    ]);
    assertDatabaseHas('engagement_responses', [
        'type' => EngagementResponseType::Sms,
        'content' => $unmatchRecordOne->body,
        'sender_id' => $student->sisid,
        'status' => EngagementResponseStatus::New,
    ]);
});

it('can check prospect phone in unmatched inbound communications if exist add engagement to the prospect', function () {
    $unmatchRecordOne = UnmatchedInboundCommunication::factory()->create([
        'sender' => '1234567890',
        'type' => EngagementResponseType::Sms,
    ]);
    UnmatchedInboundCommunication::factory()->create([
        'sender' => '0987654321',
        'type' => EngagementResponseType::Sms,
    ]);

    $prospect = Prospect::factory()->create();

    ProspectPhoneNumber::factory()->for($prospect, 'prospect')->create([
        'number' => '1234567890',
    ]);

    expect(UnmatchedInboundCommunication::count())->toBe(2);
    expect(Prospect::query()->whereHas('engagementResponses')->count())->toBe(0);
    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => '1234567890',
    ]);

    $job = new UnmatchedInboundCommunicationsJob();
    $job->handle();

    expect(UnmatchedInboundCommunication::count())->toBe(1);
    expect(Prospect::query()->whereHas('engagementResponses')->count())->toBe(1);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => '0987654321',
    ]);
    assertDatabaseHas('engagement_responses', [
        'type' => EngagementResponseType::Sms,
        'content' => $unmatchRecordOne->body,
        'sender_id' => $prospect->getKey(),
        'status' => EngagementResponseStatus::New,
    ]);
});

it('can check unmatched inbound communications with no match', function () {
    UnmatchedInboundCommunication::factory()->create(
        [
            'sender' => 'nomatch@example.com',
            'type' => EngagementResponseType::Email,
        ]
    );
    UnmatchedInboundCommunication::factory()->create(
        [
            'sender' => '9876543210',
            'type' => EngagementResponseType::Sms,
        ]
    );
    expect(UnmatchedInboundCommunication::count())->toBe(2);

    $student = Student::factory()->create();

    StudentPhoneNumber::factory()->for($student, 'student')->create([
        'number' => '1234567890',
    ]);

    StudentEmailAddress::factory()->for($student, 'student')->create([
        'address' => 'student@example.com',
    ]);

    $prospect = Prospect::factory()->create();

    ProspectEmailAddress::factory()->for($prospect, 'prospect')->create([
        'address' => 'prospect@example.com',
    ]);

    ProspectPhoneNumber::factory()->for($prospect, 'prospect')->create([
        'number' => '147852369',
    ]);

    expect(Student::query()->whereHas('engagementResponses')->exists())->toBeFalse();
    expect(Prospect::query()->whereHas('engagementResponses')->exists())->toBeFalse();
    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => 'nomatch@example.com',
        'type' => EngagementResponseType::Email,
    ]);
    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => '9876543210',
        'type' => EngagementResponseType::Sms,
    ]);

    $job = new UnmatchedInboundCommunicationsJob();
    $job->handle();

    expect(UnmatchedInboundCommunication::count())->toBe(2);
    expect(Student::query()->whereHas('engagementResponses')->count())->toBe(0);
    expect(Prospect::query()->whereHas('engagementResponses')->count())->toBe(0);
});
