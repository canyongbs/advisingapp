<?php

namespace Assist\Assistant\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\IntegrationAI\Events\AIPromptInitiated;

class LogAssistantChatMessage implements ShouldQueue
{
    public function handle(AIPromptInitiated $event): void
    {
        $prompt = $event->prompt;

        $prompt->user->assistantChatMessageLogs()->create([
            'message' => $prompt->message,
            'metadata' => $prompt->metadata,
            'request' => $prompt->request,
            'sent_at' => $prompt->timestamp,
        ]);
    }
}
