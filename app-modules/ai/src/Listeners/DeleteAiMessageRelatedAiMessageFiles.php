<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiMessageDeleted;
use AdvisingApp\Ai\Models\AiMessageFile;

class DeleteAiMessageRelatedAiMessageFiles
{
    public function handle(AiMessageDeleted $event): void
    {
        $event->message->files->each(fn (AiMessageFile $file) => $file->delete());
    }
}
