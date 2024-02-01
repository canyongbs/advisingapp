<?php

namespace AdvisingApp\Notification\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use AdvisingApp\IntegrationTwilio\DataTransferObjects\TwilioStatusCallbackData;

class CouldNotFindOutboundDeliverableFromExternalReference
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public TwilioStatusCallbackData $data
    ) {}
}
