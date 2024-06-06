<?php

namespace AdvisingApp\Ai\Services\Concerns;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiAssistant;

trait HasAiServiceHelpers
{
    public function ensureAssistantExists(AiAssistant $assistant): void
    {
        if ($this->isAssistantExisting($assistant)) {
            return;
        }

        $this->createAssistant($assistant);
        $assistant->save();
    }

    public function ensureAssistantAndThreadExists(AiThread $thread): void
    {
        $this->ensureAssistantExists($thread->assistant);

        if ($this->isThreadExisting($thread)) {
            return;
        }

        $this->createThread($thread);
        $thread->save();
    }
}
