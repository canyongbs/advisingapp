<?php

namespace AdvisingApp\Ai\Settings;

use AdvisingApp\Ai\Enums\AiModel;
use Spatie\LaravelSettings\Settings;

class AiQnaAdvisorSettings extends Settings
{
    public bool $allow_selection_of_model = true;

    public ?AiModel $preselected_model = null;

    public ?string $instructions = null;

    public ?string $background_information = null;

    public static function group(): string
    {
        return 'ai-qna-advisor';
    }
}
