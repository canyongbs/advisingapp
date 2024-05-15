<?php

namespace AdvisingApp\Ai\Enums;

use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt35Service;
use Exception;

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
}
