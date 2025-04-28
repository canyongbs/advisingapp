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

use AdvisingApp\Notification\Tests\Fixtures\TestDualNotification;
use AdvisingApp\Notification\Tests\Fixtures\TestEmailNotification;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Container\Container;
use Illuminate\Notifications\SendQueuedNotifications;

it('has the notification rate limiting applied properly for email notifications', function () {
    $recipient = Student::factory()->create();
    $notification = new TestEmailNotification();

    $job = new SendQueuedNotifications(
        $recipient, // @phpstan-ignore argument.type
        $notification,
        ['mail'],
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

it('has the notification rate limiting applied properly for sms notifications', function () {
    $recipient = Student::factory()->create();
    $notification = new TestEmailNotification();

    $job = new SendQueuedNotifications(
        $recipient, // @phpstan-ignore argument.type
        $notification,
        ['sms'],
    );

    $limiter = Container::getInstance()->make(RateLimiter::class)->limiter('notifications');

    $limits = $limiter($job);

    expect($limits) 
        ->toHaveCount(1)
        ->and($limits[0])
            ->toBeInstanceOf(Unlimited::class);
});

it('has the notification rate limiting applied properly for notifications that send to multiple channels', function () {
    $recipient = Student::factory()->create();
    $notification = new TestDualNotification();

    $job = new SendQueuedNotifications(
        $recipient, // @phpstan-ignore argument.type
        $notification,
        ['mail', 'sms'],
    );

    $limiter = Container::getInstance()->make(RateLimiter::class)->limiter('notifications');

    $limits = $limiter($job);

    expect($limits) 
        ->toHaveCount(2)
        ->and($limits[0])
            ->key->toEqual('mail')
            ->maxAttempts->toEqual(14)
            ->decaySeconds->toEqual(1)
        ->and($limits[1])
            ->toBeInstanceOf(Unlimited::class);
});
