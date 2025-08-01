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

namespace AdvisingApp\Ai\Enums;

use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt41MiniService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt41NanoService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4oMiniService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4oService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGptO1MiniService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGptO3MiniService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGptO4MiniService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGptTestService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiResponsesGpt41MiniService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiResponsesGpt41NanoService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiResponsesGpt4oMiniService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiResponsesGpt4oService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiResponsesGptO3Service;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiResponsesGptO4MiniService;
use Exception;
use Filament\Support\Contracts\HasLabel;

enum AiModel: string implements HasLabel
{
    case OpenAiGpt4o = 'openai_gpt_4o';

    case OpenAiGpt4oMini = 'openai_gpt_4o_mini';

    case OpenAiGptO1Mini = 'openai_gpt_o1_mini';

    case OpenAiGptO3 = 'openai_gpt_o3';

    case OpenAiGptO3Mini = 'openai_gpt_o3_mini';

    case OpenAiGpt41Mini = 'openai_gpt_41_mini';

    case OpenAiGpt41Nano = 'openai_gpt_41_nano';

    case OpenAiGptO4Mini = 'openai_gpt_o4_mini';

    case OpenAiGptTest = 'openai_gpt_test';

    case JinaDeepSearchV1 = 'jina_deepsearch_v1';

    case LlamaParse = 'llamaparse';

    case Test = 'test';

    public function getLabel(): ?string
    {
        $aiIntegrationSettings = app(AiIntegrationsSettings::class);

        return match ($this) {
            self::OpenAiGpt4o => $aiIntegrationSettings->open_ai_gpt_4o_model_name ?? 'Canyon 4o',
            self::OpenAiGpt4oMini => $aiIntegrationSettings->open_ai_gpt_4o_mini_model_name ?? 'Canyon 4o mini',
            self::OpenAiGptO1Mini => $aiIntegrationSettings->open_ai_gpt_o1_mini_model_name ?? 'Canyon o1 mini',
            self::OpenAiGptO3 => $aiIntegrationSettings->open_ai_gpt_o3_model_name ?? 'Canyon o3',
            self::OpenAiGptO3Mini => $aiIntegrationSettings->open_ai_gpt_o3_mini_model_name ?? 'Canyon o3 mini',
            self::OpenAiGpt41Mini => $aiIntegrationSettings->open_ai_gpt_41_mini_model_name ?? 'Canyon 4.1 mini',
            self::OpenAiGpt41Nano => $aiIntegrationSettings->open_ai_gpt_41_nano_model_name ?? 'Canyon 4.1 nano',
            self::OpenAiGptO4Mini => $aiIntegrationSettings->open_ai_gpt_o4_mini_model_name ?? 'Canyon o4 mini',
            self::JinaDeepSearchV1 => $aiIntegrationSettings->jina_deepsearch_v1_model_name ?? 'Canyon Deep Search',
            self::LlamaParse => $aiIntegrationSettings->llamaparse_model_name ?? 'Canyon Parsing Service',
            self::OpenAiGptTest => 'Canyon Test',
            self::Test => 'Test',
        };
    }

    /**
     * @return array<AiModelApplicabilityFeature>
     */
    public function getApplicableFeatures(): array
    {
        $aiIntegrationSettings = app(AiIntegrationsSettings::class);

        $features = match ($this) {
            self::OpenAiGpt4o => $aiIntegrationSettings->open_ai_gpt_4o_applicable_features,
            self::OpenAiGpt4oMini => $aiIntegrationSettings->open_ai_gpt_4o_mini_applicable_features,
            self::OpenAiGptO1Mini => $aiIntegrationSettings->open_ai_gpt_o1_mini_applicable_features,
            self::OpenAiGptO3 => $aiIntegrationSettings->open_ai_gpt_o3_applicable_features,
            self::OpenAiGptO3Mini => $aiIntegrationSettings->open_ai_gpt_o3_mini_applicable_features,
            self::OpenAiGpt41Mini => $aiIntegrationSettings->open_ai_gpt_41_mini_applicable_features,
            self::OpenAiGpt41Nano => $aiIntegrationSettings->open_ai_gpt_41_nano_applicable_features,
            self::OpenAiGptO4Mini => $aiIntegrationSettings->open_ai_gpt_o4_mini_applicable_features,
            self::JinaDeepSearchV1 => $aiIntegrationSettings->jina_deepsearch_v1_applicable_features,
            self::LlamaParse => [],
            self::OpenAiGptTest => app()->hasDebugModeEnabled() ? AiModelApplicabilityFeature::cases() : [],
            self::Test => app()->hasDebugModeEnabled() ? AiModelApplicabilityFeature::cases() : [],
        };

        return array_map(AiModelApplicabilityFeature::parse(...), $features);
    }

    public function hasService(): bool
    {
        return match ($this) {
            self::JinaDeepSearchV1, self::LlamaParse => false,
            default => true,
        };
    }

    /**
     * @return class-string<AiService>
     */
    public function getServiceClass(): string
    {
        $aiIntegrationSettings = app(AiIntegrationsSettings::class);

        return match ($this) {
            self::OpenAiGpt4o => $aiIntegrationSettings->is_open_ai_gpt_4o_responses_api_enabled ? OpenAiResponsesGpt4oService::class : OpenAiGpt4oService::class,
            self::OpenAiGpt4oMini => $aiIntegrationSettings->is_open_ai_gpt_4o_mini_responses_api_enabled ? OpenAiResponsesGpt4oMiniService::class : OpenAiGpt4oMiniService::class,
            self::OpenAiGptO1Mini => OpenAiGptO1MiniService::class,
            self::OpenAiGptO3 => OpenAiResponsesGptO3Service::class,
            self::OpenAiGptO3Mini => OpenAiGptO3MiniService::class,
            self::OpenAiGpt41Mini => $aiIntegrationSettings->is_open_ai_gpt_41_mini_responses_api_enabled ? OpenAiResponsesGpt41MiniService::class : OpenAiGpt41MiniService::class,
            self::OpenAiGpt41Nano => $aiIntegrationSettings->is_open_ai_gpt_41_nano_responses_api_enabled ? OpenAiResponsesGpt41NanoService::class : OpenAiGpt41NanoService::class,
            self::OpenAiGptO4Mini => $aiIntegrationSettings->is_open_ai_gpt_o4_mini_responses_api_enabled ? OpenAiResponsesGptO4MiniService::class : OpenAiGptO4MiniService::class,
            self::OpenAiGptTest => OpenAiGptTestService::class,
            self::Test => TestAiService::class,
            default => throw new Exception('No Service class found for this model.'),
        };
    }

    public function getService(): AiService
    {
        return app($this->getServiceClass());
    }

    public function supportsAssistantFileUploads(): bool
    {
        // TODO: Not actually sure mini supports files, need to confirm
        return match ($this) {
            self::OpenAiGpt4o, self::OpenAiGpt4oMini, self::OpenAiGptO1Mini, self::OpenAiGptO3Mini, self::OpenAiGpt41Mini, self::OpenAiGpt41Nano, self::OpenAiGptO4Mini => true,
            default => false,
        };
    }

    public function isSharedDeployment(AiModel $model): bool
    {
        if ($this === $model) {
            return true;
        }

        $deployment = $model->getService()->getDeployment();

        if (blank($deployment)) {
            return false;
        }

        return $deployment === $this->getService()->getDeployment();
    }

    public static function parse(string | self | null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}
