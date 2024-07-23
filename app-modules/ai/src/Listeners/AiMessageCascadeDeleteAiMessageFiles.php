<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageTrashed;

class AiMessageCascadeDeleteAiMessageFiles
{
    public function handle(AiMessageTrashed $event): void
    {
        $event->aiMessage->files()->lazyById()->each(
            fn (AiMessageFile $file) => $file->delete()
        );
    }
}
