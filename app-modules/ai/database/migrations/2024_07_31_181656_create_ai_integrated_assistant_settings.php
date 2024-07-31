<?php

use AdvisingApp\Ai\Enums\AiModel;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('ai-integrated-assistant.default_model', AiModel::OpenAiGpt4o);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai-integrated-assistant.default_model');
    }
};
