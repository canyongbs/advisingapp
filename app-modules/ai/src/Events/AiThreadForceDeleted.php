<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\DispatchAiThreadExternalCleanup;

class AiThreadForceDeleted
{
    use Dispatchable;

    public const LISTENERS = [
        DispatchAiThreadExternalCleanup::class,
    ];

    public function __construct(public AiThread $aiThread) {}
}
