<?php

namespace AdvisingApp\Assistant\Actions;

use OpenAI\Responses\Assistants\AssistantResponse;
use AdvisingApp\IntegrationAI\Client\Contracts\AiChatClient;

class GetAiAssistantFromID
{
    public function __construct(
        private AiChatClient $ai
    ) {}

    public function get(string $id): AssistantResponse
    {
        return $this->ai->client->assistants()->retrieve($id);
    }
}
