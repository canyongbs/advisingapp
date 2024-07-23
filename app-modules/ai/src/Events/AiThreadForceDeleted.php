<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Listeners\DispatchAiThreadExternalCleanup;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class AiThreadForceDeleted
{
    use Dispatchable;

    public const LISTENERS = [
        DispatchAiThreadExternalCleanup::class,
    ];

    public function __construct(public AiThread $aiThread) {}
}
