<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Events\AiThreadTrashed;

class AiThreadCascadeDeleteAiMessages
{
    public function handle(AiThreadTrashed $event): void
    {
        $event->aiThread->messages()->lazyById()->each(
            fn (AiMessage $message) => $message->delete()
        );
    }
}
