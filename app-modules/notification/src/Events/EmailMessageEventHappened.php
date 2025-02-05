<?php

namespace AdvisingApp\Notification\Events;

use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailMessageEventHappened
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public EmailMessageEventType $type,
        public EmailMessage $emailMessage
    ) {}
}
