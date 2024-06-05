<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('ai.open_ai_gpt_35_base_uri', config('integration-open-ai.gpt_35_base_uri'), encrypted: true);
        $this->migrator->add('ai.open_ai_gpt_35_api_key', config('integration-open-ai.gpt_35_api_key'), encrypted: true);
        $this->migrator->add('ai.open_ai_gpt_35_model', config('integration-open-ai.gpt_35_model'), encrypted: true);

        $this->migrator->add('ai.open_ai_gpt_4_base_uri', config('integration-open-ai.gpt_4_base_uri'), encrypted: true);
        $this->migrator->add('ai.open_ai_gpt_4_api_key', config('integration-open-ai.gpt_4_api_key'), encrypted: true);
        $this->migrator->add('ai.open_ai_gpt_4_model', config('integration-open-ai.gpt_4_model'), encrypted: true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_base_uri');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_api_key');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_model');

        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_base_uri');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_api_key');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_model');
    }
};
