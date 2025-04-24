<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        try {
            $this->migrator->add('ai.open_ai_gpt_41_nano_base_uri', config('integration-open-ai.gpt_41_nano_base_uri'), encrypted: true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('ai.open_ai_gpt_41_nano_api_key', config('integration-open-ai.gpt_41_nano_api_key'), encrypted: true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('ai.open_ai_gpt_41_nano_model', config('integration-open-ai.gpt_41_nano_model'), encrypted: true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai.open_ai_gpt_41_nano_base_uri');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_41_nano_api_key');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_41_nano_model');
    }
};
