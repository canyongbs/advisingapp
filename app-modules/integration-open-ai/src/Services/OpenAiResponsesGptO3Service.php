<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationOpenAi\Services;

class OpenAiResponsesGptO3Service extends BaseOpenAiResponsesService
{
    public function getApiKey(): string
    {
        return $this->settings->open_ai_gpt_o3_api_key ?? config('integration-open-ai.gpt_o3_api_key');
    }

    public function getModel(): string
    {
        return $this->settings->open_ai_gpt_o3_model ?? config('integration-open-ai.gpt_o3_model');
    }

    public function getDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_o3_base_uri ?? config('integration-open-ai.gpt_o3_base_uri');
    }

    public function hasReasoning(): bool
    {
        return true;
    }

    public function hasTemperature(): bool
    {
        return false;
    }

    public function getImageDeploymentHeader(): ?array
    {
        if (! $this->hasImageGeneration()) {
            return null;
        }

        return [
            'x-ms-oai-image-generation-deployment' => $this->getImageGenerationDeployment(),
        ];
    }

    public function hasImageGeneration(): bool
    {
        return filled($this->getImageGenerationModel());
    }

    public function getImageGenerationModel(): ?string
    {
        return $this->settings->open_ai_gpt_o3_image_generation_model
            ?? config('integration-open-ai.gpt_o3_image_generation_model');
    }

    public function getImageGenerationDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_o3_image_generation_model
            ?? config('integration-open-ai.gpt_o3_image_generation_model');
    }
}
