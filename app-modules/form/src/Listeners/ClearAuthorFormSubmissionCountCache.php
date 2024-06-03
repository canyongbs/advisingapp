<?php

namespace AdvisingApp\Form\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use AdvisingApp\Form\Events\FormSubmissionCreated;

class ClearAuthorFormSubmissionCountCache implements ShouldQueue
{
    public function handle(FormSubmissionCreated $event): void
    {
        if (! is_null($event->submission->author)) {
            Cache::tags('form-submission-count')
                ->forget(
                    "form-submission-count-{$event->submission->author->getKey()}"
                );
        }
    }
}
