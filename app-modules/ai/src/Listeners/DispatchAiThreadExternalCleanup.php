<?php

namespace AdvisingApp\Ai\Listeners;

use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Events\AiThreadForceDeleted;
use AdvisingApp\Ai\Jobs\DeleteExternalAiThread;
use AdvisingApp\Ai\Jobs\DeleteAiThreadVectorStores;

class DispatchAiThreadExternalCleanup
{
    public function handle(AiThreadForceDeleted $event): void
    {
        Bus::chain([
            new DeleteAiThreadVectorStores($event->aiThread),
            new DeleteExternalAiThread($event->aiThread),
        ])
            ->dispatch();
    }
}
