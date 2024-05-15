<?php

namespace AdvisingApp\IntegrationOpenAi\Services;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService;
use OpenAI;

class OpenAiGpt35Service extends BaseOpenAiService
{
    public function __construct()
    {
        $this->client = OpenAI::factory()
            ->withBaseUri(config('integration-open-ai.gpt_35_api_base_uri'))
            ->withApiKey(config('integration-open-ai.gpt_35_api_key'))
            ->withQueryParam('api-version', config('integration-open-ai.gpt_35_api_version'))
            ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
            ->withHttpHeader('Accept', '*/*')
            ->make();
    }

    public function getModel(): string
    {
        return 'gpt-3.5-turbo';
    }
}
