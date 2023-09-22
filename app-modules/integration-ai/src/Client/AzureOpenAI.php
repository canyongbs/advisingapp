<?php

namespace Assist\IntegrationAI\Client;

use OpenAI;

class AzureOpenAI extends BaseAIChatClient
{
    protected function initializeClient(): void
    {
        $this->baseEndpoint = config('services.azure_open_ai.endpoint');
        $this->apiKey = config('services.azure_open_ai.api_key');
        $this->apiVersion = config('services.azure_open_ai.api_version');
        $this->deployment = config('services.azure_open_ai.deployment_name');

        $this->client = OpenAI::factory()
            ->withBaseUri("{$this->baseEndpoint}/openai/deployments/{$this->deployment}")
            ->withHttpHeader('api-key', $this->apiKey)
            ->withQueryParam('api-version', $this->apiVersion)
            ->make();
    }
}
