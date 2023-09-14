<?php

namespace Assist\IntegrationAI\Settings;

use Spatie\LaravelSettings\Settings;

class AISettings extends Settings
{
    public string $prompt_context;

    public int $max_tokens;

    public int $temperature;

    public static function group(): string
    {
        return 'ai';
    }
}
