<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Events\AiThreadTrashed;
use AdvisingApp\Ai\Events\AiThreadForceDeleting;

class AiThreadCascadeDeleteAiMessages
{
    public function handle(AiThreadTrashed|AiThreadForceDeleting $event): void
    {
        $event->aiThread->messages()->lazyById()->each(
            fn (AiMessage $message) => $event->aiThread->isForceDeleting()
                ? $message->forceDelete()
                : $message->delete()
        );
    }
}
