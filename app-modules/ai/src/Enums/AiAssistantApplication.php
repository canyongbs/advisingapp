<?php

namespace AdvisingApp\Ai\Enums;

use AdvisingApp\Ai\Settings\AiSettings;
use Filament\Support\Contracts\HasLabel;

enum AiAssistantApplication: string implements HasLabel
{
    case PersonalAssistant = 'personal_assistant';

    case Test = 'test';

    public function getLabel(): string
    {
        return match ($this) {
            self::PersonalAssistant => 'Personal Assistant',
            self::Test => 'Test',
        };
    }

    public static function getDefault(): self
    {
        return self::PersonalAssistant;
    }

    public function getDefaultModel(): AiModel
    {
        $settings = app(AiSettings::class);

        return match ($this) {
            self::PersonalAssistant => $settings->default_model ?? AiModel::OpenAiGpt4o,
            self::Test => AiModel::Test,
        };
    }

    public static function parse(string | self | null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}
