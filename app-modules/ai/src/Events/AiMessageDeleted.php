<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class AiMessageDeleted
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(public AiMessage $message)
    {
        // test
    }
}
