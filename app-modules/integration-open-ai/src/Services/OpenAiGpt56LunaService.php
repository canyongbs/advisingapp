<?php

namespace AdvisingApp\IntegrationOpenAi\Services;

class OpenAiGpt56LunaService extends BaseOpenAiService
{
    public function getApiKey(): string
    {
        return $this->settings->open_ai_gpt_56_luna_api_key ?? config('integration-open-ai.gpt_56_luna_api_key');
    }

    public function getModel(): string
    {
        return $this->settings->open_ai_gpt_56_luna_model ?? config('integration-open-ai.gpt_56_luna_model');
    }

    public function getDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_56_luna_base_uri ?? config('integration-open-ai.gpt_56_luna_base_uri');
    }

    public function getImageGenerationDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_56_luna_image_generation_deployment;
    }
}
