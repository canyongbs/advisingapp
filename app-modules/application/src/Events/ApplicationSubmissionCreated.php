<?php

namespace Assist\Application\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Assist\Application\Models\ApplicationSubmission;

class ApplicationSubmissionCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public ApplicationSubmission $submission
    ) {}
}
