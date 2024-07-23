<?php

namespace AdvisingApp\Ai\Listeners;

use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Events\AiThreadForceDeleted;
use AdvisingApp\Ai\Jobs\DeleteAiThreadVectorStores;

class DispatchAiThreadExternalCleanup
{
    public function handle(AiThreadForceDeleted $event): void
    {
        Bus::dispatch(new DeleteAiThreadVectorStores($event->aiThread));
    }
}
