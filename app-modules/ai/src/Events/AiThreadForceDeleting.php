<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\DeleteExternalAiThread;
use AdvisingApp\Ai\Listeners\DeleteAiThreadVectorStores;

class AiThreadForceDeleting
{
    use Dispatchable;

    public const LISTENERS = [
        DeleteAiThreadVectorStores::class,
        DeleteExternalAiThread::class,
    ];

    public function __construct(public AiThread $aiThread) {}
}
