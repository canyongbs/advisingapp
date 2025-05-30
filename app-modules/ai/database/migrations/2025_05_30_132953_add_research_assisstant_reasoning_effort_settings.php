<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        try {
            $this->migrator->add('ai_research_assistant.reasoning_effort', 'high');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai_research_assistant.reasoning_effort');
    }
};
