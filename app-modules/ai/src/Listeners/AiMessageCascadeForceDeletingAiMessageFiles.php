<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageForceDeleting;

class AiMessageCascadeForceDeletingAiMessageFiles
{
    public function handle(AiMessageForceDeleting $event): void
    {
        $event->aiMessage->files()->lazyById()->each(
            fn (AiMessageFile $file) => $file->forceDelete()
        );
    }
}
