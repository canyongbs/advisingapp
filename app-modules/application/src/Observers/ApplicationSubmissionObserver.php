<?php

namespace Assist\Application\Observers;

use Illuminate\Support\Facades\Event;
use Assist\Application\Models\ApplicationSubmission;
use Assist\Application\Events\ApplicationSubmissionCreated;

class ApplicationSubmissionObserver
{
    public function created(ApplicationSubmission $submission): void
    {
        Event::dispatch(
            event: new ApplicationSubmissionCreated(submission: $submission)
        );
    }
}
