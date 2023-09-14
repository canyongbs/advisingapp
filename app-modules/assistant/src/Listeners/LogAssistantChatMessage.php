<?php

namespace Assist\Assistant\Listeners;

use Assist\IntegrationAI\Events\AIPromptInitiated;

class LogAssistantChatMessage
{
    public function handle(AIPromptInitiated $event): void
    {
        $prompt = $event->prompt;

        $prompt->user->assistantChatMessageLogs()->create([
            'message' => $prompt->message,
            'metadata' => $prompt->metadata,
            'request' => [
                'ip' => $prompt->request->ip(),
                'headers' => $prompt->request->headers->all(),
            ],
        ]);
    }
}
