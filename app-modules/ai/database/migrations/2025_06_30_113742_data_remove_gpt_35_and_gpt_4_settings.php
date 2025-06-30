<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_base_uri');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_api_key');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_model');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_model_name');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_applicable_features');

        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_base_uri');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_api_key');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_model');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_model_name');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_applicable_features');
    }

    public function down(): void
    {
        $this->migrator->add('ai.open_ai_gpt_35_base_uri');
        $this->migrator->add('ai.open_ai_gpt_35_api_key');
        $this->migrator->add('ai.open_ai_gpt_35_model');
        $this->migrator->add('ai.open_ai_gpt_35_model_name');
        $this->migrator->add('ai.open_ai_gpt_35_applicable_features', []);

        $this->migrator->add('ai.open_ai_gpt_4_base_uri');
        $this->migrator->add('ai.open_ai_gpt_4_api_key');
        $this->migrator->add('ai.open_ai_gpt_4_model');
        $this->migrator->add('ai.open_ai_gpt_4_model_name');
        $this->migrator->add('ai.open_ai_gpt_4_applicable_features', []);
    }
};
