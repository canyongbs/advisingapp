<?php

namespace AdvisingApp\Assistant\Actions;

use OpenAI\Client;
use AdvisingApp\IntegrationAI\Client\Contracts\AiChatClient;
use AdvisingApp\Assistant\DataTransferObjects\AiAssistantUpdateData;

class UpdateAiAssistant
{
    public function __construct(
        private AiChatClient $ai
    ) {}

    public function from(AiAssistantUpdateData $data): void
    {
        /** @var Client $client */
        $client = $this->ai->client;

        $response = $client->assistants()->modify(resolve(GetAiAssistant::class)->get(), [
            ...array_filter($data->toArray()),
            'metadata' => [
                'last_updated_at' => now(),
            ],
        ]);
    }
}
