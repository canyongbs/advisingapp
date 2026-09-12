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

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Jobs\UnmatchedInboundCommunicationsJob;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\UnmatchedInboundCommunication;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;

function unmatchedEmailFrom(string $address): UnmatchedInboundCommunication
{
    return UnmatchedInboundCommunication::factory()->create([
        'sender' => $address,
        'type' => EngagementResponseType::Email,
        'subject' => 'Re: your message',
        'body' => 'Replying to you.',
        'occurred_at' => now(),
    ]);
}

it('matches an unmatched email to an active student', function () {
    $student = Student::factory()->create();

    $address = StudentEmailAddress::factory()
        ->for($student, 'student')
        ->create()
        ->address;

    $communication = unmatchedEmailFrom($address);

    assertDatabaseCount(EngagementResponse::class, 0);

    app(UnmatchedInboundCommunicationsJob::class)->handle();

    // Matched, so it becomes a response on the student and stops being unmatched.
    assertDatabaseCount(EngagementResponse::class, 1);
    assertModelMissing($communication);
});

it('does not match an unmatched email to an archived student', function () {
    $student = Student::factory()->create();

    $address = StudentEmailAddress::factory()
        ->for($student, 'student')
        ->create()
        ->address;

    $student->archive();

    $communication = unmatchedEmailFrom($address);

    app(UnmatchedInboundCommunicationsJob::class)->handle();

    // No response is attributed to the archived student, and the message is kept for review.
    assertDatabaseCount(EngagementResponse::class, 0);
    assertModelExists($communication);
});
