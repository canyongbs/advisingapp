<?php

use AdvisingApp\Notification\Tests\Fixtures\TestEmailNotification;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

it('modifies the SendQueuedNotifications job properly', function () {
    Queue::fake();

    $recipient = Student::factory()->create();
    $notification = new TestEmailNotification();

    $recipient->notify($notification);

    Queue::assertPushed(SendQueuedNotifications::class, function ($job) use ($recipient, $notification) {
        return $job->notification::class === $notification::class
            && $job->notifiables->count() === 1
            && $job->notifiables->first()->is($recipient)
            && $job->channels === ['mail']
            && $job->tries === 15
            && $job->maxExceptions === 3;
    });
});
