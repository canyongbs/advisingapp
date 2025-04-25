<?php

use AdvisingApp\Notification\Tests\Fixtures\TestEmailNotification;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Container\Container;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

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

// TODO: checking if the limits are setup properly if it ever dispatches SMS and email at the same time

it('modifies the SendQueuedNotifications job properly', function () {
    Queue::fake();

    $recipient = Student::factory()->create();
    $notification = new TestEmailNotification();

    $recipient->notify($notification);

    Queue::assertPushed(SendQueuedNotifications::class, function ($job) use ($recipient, $notification) {
        return $job->notification::class === $notification::class
            && $job->notifiables->count() === 1
            && $job->notifiables->first()->is($recipient)
            && $job->channels === ['mail'];
    });
});

// TODO: Test that the tries, maxExceptions, etc. is properly set