<?php

namespace Assist\Form\Events;

use Assist\Form\Models\FormSubmission;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class FormSubmissionCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public FormSubmission $submission
    ) {}
}
