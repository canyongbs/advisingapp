<?php

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Assist\Form\Models\FormSubmission;
use Assist\Form\Events\FormSubmissionCreated;
use Assist\Form\Listeners\NotifySubscribersOfFormSubmission;
use Assist\Form\Notifications\AuthorLinkedFormSubmissionCreatedNotification;

it('dispatches FormSubmissionCreated Event when a FormSubmission is created', function () {
    Event::fake(FormSubmissionCreated::class);

    $submission = FormSubmission::factory()->create();

    Event::assertDispatched(
        event: FormSubmissionCreated::class,
        callback: fn (FormSubmissionCreated $event) => $event->submission->is($submission)
    );
});

test('FormSubmissionCreated Event has the proper listeners', function () {
    Event::fake();

    Event::assertListening(
        expectedEvent: FormSubmissionCreated::class,
        expectedListener: NotifySubscribersOfFormSubmission::class
    );
});

test('NotifySubscribersOfFormSubmission dispatches the correct Notification', function () {
    Notification::fake();

    $submission = FormSubmission::factory()->make();

    $user = User::factory()->create();

    $user->subscriptions()->create([
        'subscribable_id' => $submission->author->getKey(),
        'subscribable_type' => $submission->author->getMorphClass(),
    ]);

    $submission->save();

    $submission->author->subscriptions->map(fn ($subscription) => $subscription->user)->each(function (User $user) use ($submission) {
        Notification::assertSentTo(notifiable: $user, notification: AuthorLinkedFormSubmissionCreatedNotification::class);
    });
});
