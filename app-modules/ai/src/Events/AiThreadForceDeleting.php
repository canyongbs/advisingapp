<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\AiThreadCascadeForceDeletingAiMessages;

class AiThreadForceDeleting
{
    use Dispatchable;

    public const LISTENERS = [
        AiThreadCascadeForceDeletingAiMessages::class,
    ];

    public function __construct(public AiThread $aiThread) {}
}
