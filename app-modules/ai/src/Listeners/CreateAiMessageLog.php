<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiMessageCreated;
use AdvisingApp\Ai\Models\LegacyAiMessageLog;

class CreateAiMessageLog
{
    public function handle(AiMessageCreated $event): void
    {
        $message = $event->aiMessage;

        if (! $message->user || ! $message->request) {
            return;
        }

        LegacyAiMessageLog::create([
            'message' => $message->content,
            'metadata' => [
                'context' => $message->context,
            ],
            'request' => $message->request,
            'sent_at' => now(),
            'user_id' => $message->user_id,
        ]);
    }
}
