<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiMessageDeleted;

class DeleteAiMessageRelatedAiMessageFiles
{
    public function handle(AiMessageDeleted $event): void
    {
        $event->message->files;
    }
}
