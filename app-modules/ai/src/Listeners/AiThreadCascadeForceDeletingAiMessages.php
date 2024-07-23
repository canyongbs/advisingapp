<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AiThreadCascadeForceDeletingAiMessages
{
    public function handle(AiThreadForceDeleting $event): void
    {
        $event->aiThread->messages()->lazyById()->each(
            fn (AiMessage $message) => $message->forceDelete()
        );
    }
}
