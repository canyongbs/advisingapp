<?php

namespace AdvisingApp\Ai\Services;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;

class TestAiService implements Contracts\AiService
{
    public function createAssistant(AiAssistant $assistant): void
    {
    }

    public function updateAssistant(AiAssistant $assistant): void
    {
    }

    public function createThread(AiThread $thread): void
    {
    }

    public function sendMessage(AiMessage $message): AiMessage
    {
        $response = new AiMessage();
        $response->content = fake()->paragraph();

        return $response;
    }
}
