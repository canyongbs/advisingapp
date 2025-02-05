<?php

namespace AdvisingApp\Notification\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public 
    ) {}
}
