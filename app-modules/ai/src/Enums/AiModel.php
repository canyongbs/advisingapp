<?php

namespace AdvisingApp\Ai\Enums;

use Exception;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt35Service;

enum AiModel: string
{
    case OpenAiGpt35 = 'openai_gpt_3.5';

    case Test = 'test';

    public function getService(): AiService
    {
        $service = match ($this) {
            self::OpenAiGpt35 => OpenAiGpt35Service::class,
            self::Test => TestAiService::class,
            default => throw new Exception('AI model service has not been implemented yet.'),
        };

        app()->scopedIf($service);

        return app($service);
    }

    public function isVisibleForApplication(AiApplication $aiApplication): bool
    {
        return match ($this) {
            self::OpenAiGpt35 => $aiApplication === AiApplication::PersonalAssistant,
            self::Test => true,
            default => throw new Exception('AI model visibility for application has not been implemented yet.'),
        };
    }
}
