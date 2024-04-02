<?php

namespace AdvisingApp\Assistant\Actions;

use OpenAI\Client;
use Spatie\Multitenancy\Models\Tenant;
use AdvisingApp\IntegrationAI\Settings\AISettings;
use AdvisingApp\IntegrationAI\Client\Contracts\AiChatClient;

class CreateAiAssistant
{
    public function __construct(
        private AiChatClient $ai
    ) {}

    public function create()
    {
        /** @var Client $client */
        $client = $this->ai->client;

        $tenant = Tenant::current();

        $settings = resolve(AISettings::class);

        /** @var AssistantResponse $response */
        $assistantResponse = $client->assistants()->create([
            'name' => "{$tenant->name} AI Assistant",
            'description' => "An AI Assistant for {$tenant->name}",
            'instructions' => $settings->prompt_system_context,
            'model' => config('services.azure_open_ai.personal_assistant_deployment_name'),
            'metadata' => [
                'last_updated_at' => now(),
            ],
        ]);

        $settings->assistant_id = $assistantResponse->id;
        $settings->save();

        return $settings->assistant_id;
    }
}
