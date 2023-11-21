<?php

namespace Assist\Form\Listeners;

use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Form\Events\FormSubmissionCreated;
use Assist\Notifications\Models\Subscription;
use Assist\Form\Notifications\AuthorLinkedFormSubmissionCreatedNotification;

class NotifySubscribersOfFormSubmission implements ShouldQueue
{
    public function handle(FormSubmissionCreated $event): void
    {
        /** @var Student|Prospect|null $author */
        $author = $event->submission->author;

        $author?->subscriptions?->each(function (Subscription $subscription) use ($event) {
            $subscription->user->notify(new AuthorLinkedFormSubmissionCreatedNotification(submission: $event->submission));
        });
    }
}
