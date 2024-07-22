<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiMessageForceDeleting;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageTrashed;

class AiMessageCascadeDeleteAiMessageFiles
{
    public function handle(AiMessageTrashed|AiMessageForceDeleting $event): void
    {
        $event->aiMessage->files()->lazyById()->each(
            fn (AiMessageFile $file) => $event->aiMessage->isForceDeleting()
                ? $file->forceDelete()
                : $file->delete()
        );
    }
}
