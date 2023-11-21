<?php

namespace Assist\Form\Observers;

use Illuminate\Support\Facades\Event;
use Assist\Form\Models\FormSubmission;
use Assist\Form\Events\FormSubmissionCreated;

class FormSubmissionObserver
{
    public function created(FormSubmission $submission): void
    {
        Event::dispatch(
            event: new FormSubmissionCreated(submission: $submission)
        );
    }
}
