<?php

namespace AdvisingApp\IntegrationOpenAi\Services;

class OpenAiResponsesGpt5MiniService extends BaseOpenAiResponsesService
{
    public function getApiKey(): string
    {
        return $this->settings->open_ai_gpt_5_mini_api_key ?? config('integration-open-ai.gpt_5_mini_api_key');
    }

    public function getModel(): string
    {
        return $this->settings->open_ai_gpt_5_mini_model ?? config('integration-open-ai.gpt_5_mini_model');
    }

    public function getDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_5_mini_base_uri ?? config('integration-open-ai.gpt_5_mini_base_uri');
    }
}
