<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('ai.open_ai_gpt_35_version', config('integration-open-ai.gpt_35_api_version'));
        $this->migrator->add('ai.open_ai_gpt_4_version', config('integration-open-ai.gpt_4_api_version'));
        $this->migrator->add('ai.open_ai_gpt_4o_version', config('integration-open-ai.gpt_4o_api_version'));
        $this->migrator->add('ai.open_ai_gpt_4o_mini_version', config('integration-open-ai.gpt_4o_mini_api_version'));
        $this->migrator->add('ai.open_ai_gpt_o1_mini_version', config('integration-open-ai.gpt_o1_mini_api_version'));
        $this->migrator->add('ai.open_ai_gpt_o3_mini_version', config('integration-open-ai.gpt_o3_mini_api_version'));
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai.open_ai_gpt_35_version');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4_version');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4o_version');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4o_mini_version');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_o1_mini_version');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_o3_mini_version');
    }
};
