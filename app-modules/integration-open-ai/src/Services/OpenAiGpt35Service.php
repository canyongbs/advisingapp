<?php

namespace AdvisingApp\IntegrationOpenAi\Services;

use OpenAI;

class OpenAiGpt35Service extends BaseOpenAiService
{
    public function __construct()
    {
        $this->client = OpenAI::factory()
            ->withBaseUri(config('integration-open-ai.gpt_35_base_uri'))
            ->withHttpHeader('api-key', config('integration-open-ai.gpt_35_api_key'))
            ->withQueryParam('api-version', config('integration-open-ai.gpt_35_api_version'))
            ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
            ->withHttpHeader('Accept', '*/*')
            ->make();
    }

    public function getModel(): string
    {
        return config('integration-open-ai.gpt_35_model');
    }
}
