<?php

use AdvisingApp\Notification\Tests\Fixtures\TestEmailNotification;
use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\freezeTime;

it('modifies the SendQueuedNotifications job properly', function () {
    Queue::fake();

    $recipient = Student::factory()->create();
    $notification = new TestEmailNotification();

    freezeTime(function () use ($recipient, $notification) {
        $recipient->notify($notification);

        Queue::assertPushed(SendQueuedNotifications::class, function ($job) use ($recipient, $notification) {
            return $job->notification::class === $notification::class
                && $job->notifiables->count() === 1
                && $job->notifiables->first()->is($recipient)
                && $job->channels === ['mail']
                && $job->retryUntil() instanceof Carbon && $job->retryUntil()->equalTo(now()->addHours(2))
                && $job->maxExceptions === 3;
        }); 
    });
});
