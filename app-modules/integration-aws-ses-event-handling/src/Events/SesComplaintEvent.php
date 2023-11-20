<?php

namespace Assist\IntegrationAwsSesEventHandling\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Assist\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;

class SesComplaintEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public SesEventData $data,
    ) {}
}
