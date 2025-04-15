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

use AdvisingApp\Engagement\Jobs\CreateBatchedEngagement;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Engagement\Notifications\EngagementNotification;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;

it('will create and send an engagement immediately', function () {
    Notification::fake();

    $engagementBatch = EngagementBatch::factory()->deliverNow()->create();
    $recipient = Student::factory()->create();

    assertDatabaseCount(Engagement::class, 0);

    dispatch(app(CreateBatchedEngagement::class, [
        'engagementBatch' => $engagementBatch,
        'recipient' => $recipient,
    ]));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->engagementBatch->is($engagementBatch)->toBeTrue()
        ->user->is($engagementBatch->user)->toBeTrue()
        ->recipient->is($recipient)->toBeTrue()
        ->channel->toBe($engagementBatch->channel)
        ->subject->toBe($engagementBatch->subject)
        ->body->toMatchArray($engagementBatch->body)
        ->scheduled_at->toBeNull()
        ->dispatched_at->not->toBeNull();

    Notification::assertSentTo(
        $recipient,
        EngagementNotification::class
    );
});

it('will create but not dispatch a scheduled engagement', function () {
    Notification::fake();

    $engagementBatch = EngagementBatch::factory()->create([
        'scheduled_at' => now()->addMinute(),
    ]);
    $recipient = Student::factory()->create();

    assertDatabaseCount(Engagement::class, 0);

    dispatch(app(CreateBatchedEngagement::class, [
        'engagementBatch' => $engagementBatch,
        'recipient' => $recipient,
    ]));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->engagementBatch->is($engagementBatch)->toBeTrue()
        ->user->is($engagementBatch->user)->toBeTrue()
        ->recipient->is($recipient)->toBeTrue()
        ->channel->toBe($engagementBatch->channel)
        // ->subject->toBe($engagementBatch->subject)
        ->subject->toMatchArray($engagementBatch->subject)
        ->body->toMatchArray($engagementBatch->body)
        ->scheduled_at->startOfSecond()->eq($engagementBatch->scheduled_at->startOfSecond())->toBeTrue()
        ->dispatched_at->toBeNull();

    Notification::assertNotSentTo(
        $recipient,
        EngagementNotification::class
    );
});

it('has the notification rate limiting applied properly for email batched engagements', function () {
    $engagementBatch = EngagementBatch::factory()->email()->create();
    $recipient = Student::factory()->create();

    $job = new CreateBatchedEngagement(
        $engagementBatch,
        $recipient
    );

    $limiter = Container::getInstance()->make(RateLimiter::class)->limiter('notifications');

    $limits = $limiter($job);

    /** @phpstan-ignore property.notFound */
    expect($limits)
        ->toHaveCount(1)
        ->and($limits[0])
        ->key->toEqual('mail')
        ->maxAttempts->toEqual(14)
        ->decaySeconds->toEqual(1);
});

it('has the notification rate limiting applied properly for sms batched engagements', function () {
    $engagementBatch = EngagementBatch::factory()->sms()->create();
    $recipient = Student::factory()->create();

    $job = new CreateBatchedEngagement(
        $engagementBatch,
        $recipient
    );

    $limiter = Container::getInstance()->make(RateLimiter::class)->limiter('notifications');

    $limits = $limiter($job);

    expect($limits)
        ->toHaveCount(1)
        ->and($limits[0])
        ->toBeInstanceOf(Unlimited::class);
});
