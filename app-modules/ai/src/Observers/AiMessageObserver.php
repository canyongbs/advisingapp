<?php

namespace AdvisingApp\Ai\Observers;

use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\LegacyAiMessageLog;

class AiMessageObserver
{
    public function created(AiMessage $message): void
    {
        if (! $message->user) {
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
