<?php

namespace AdvisingApp\Form\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use AdvisingApp\Form\Events\FormSubmissionCreated;

class ClearFormFormSubmissionCountCache implements ShouldQueue
{
    public function handle(FormSubmissionCreated $event): void
    {
        Cache::tags('form-submission-count')
            ->forget(
                "form-submission-count-{$event->submission->submissible->getKey()}"
            );
    }
}
