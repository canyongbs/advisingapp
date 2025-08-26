<?php

namespace AdvisingApp\IntegrationOpenAi\Services;

class OpenAiResponsesGpt5NanoService extends BaseOpenAiResponsesService
{
    public function getApiKey(): string
    {
        return $this->settings->open_ai_gpt_5_nano_api_key ?? config('integration-open-ai.gpt_5_nano_api_key');
    }

    public function getModel(): string
    {
        return $this->settings->open_ai_gpt_5_nano_model ?? config('integration-open-ai.gpt_5_nano_model');
    }

    public function getDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_5_nano_base_uri ?? config('integration-open-ai.gpt_5_nano_base_uri');
    }
}
