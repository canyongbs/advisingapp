<?php

namespace AdvisingApp\Ai\Settings;

use Spatie\LaravelSettings\Settings;

class AiQnAAdvisorSettings extends Settings
{
    public ?string $instructions = null;

    public ?string $background_information = null;

    public static function group(): string
    {
        return 'ai-qna-advisor';
    }
}
