<?php

namespace AdvisingApp\Ai\Enums;

enum AiThreadLockedReason: string
{
    case AssistantUpdated = 'assistant_updated';

    public function getMessage(): string
    {
        return match ($this) {
            AiThreadLockedReason::AssistantUpdated => 'This thread has been locked, as the advisor has been updated with new knowledge. Start a new thread to continue the conversation.',
        };
    }
}
