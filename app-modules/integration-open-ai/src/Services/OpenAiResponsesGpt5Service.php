<?php

namespace AdvisingApp\IntegrationOpenAi\Services;

class OpenAiResponsesGpt5Service extends BaseOpenAiResponsesService
{
    public function getApiKey(): string
    {
        return $this->settings->open_ai_gpt_5_api_key ?? config('integration-open-ai.gpt_5_api_key');
    }

    public function getModel(): string
    {
        return $this->settings->open_ai_gpt_5_model ?? config('integration-open-ai.gpt_5_model');
    }

    public function getDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_5_base_uri ?? config('integration-open-ai.gpt_5_base_uri');
    }
}
