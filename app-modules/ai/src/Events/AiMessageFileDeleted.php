<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessageFile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class AiMessageFileDeleted
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(public AiMessageFile $aiMessageFile)
    {
    }
}
