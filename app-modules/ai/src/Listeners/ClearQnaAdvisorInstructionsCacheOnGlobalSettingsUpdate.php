<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Settings\AiQnaAdvisorSettings;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelSettings\Events\SettingsSaved;

class ClearQnaAdvisorInstructionsCacheOnGlobalSettingsUpdate
{
    public function handle(SettingsSaved $event): void
    {
        if ($event->settings instanceof AiQnaAdvisorSettings) {
            Cache::tags(['qna_advisor_instructions'])->flush();
        }
    }
}
