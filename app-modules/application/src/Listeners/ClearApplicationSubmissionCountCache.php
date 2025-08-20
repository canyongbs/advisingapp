<?php

namespace AdvisingApp\Application\Listeners;

use Illuminate\Support\Facades\Cache;
use AdvisingApp\Application\Events\ApplicationSubmissionCreated;

class ClearApplicationSubmissionCountCache
{
    public function handle(ApplicationSubmissionCreated $event): void
    {
        Cache::tags('{application-submission-count}')
            ->forget(
                "applciation-submission-count-{$event->submission->submissible->getKey()}"
            );
    }
}
