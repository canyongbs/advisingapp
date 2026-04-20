<?php

use App\Features\Gpt54MiniFeature;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            try {
                $this->migrator->add('ai.open_ai_gpt_54_mini_model_name');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_54_mini_base_uri', config('integration-open-ai.gpt_54_mini_base_uri'), encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_54_mini_api_key', config('integration-open-ai.gpt_54_mini_api_key'), encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_54_mini_model', config('integration-open-ai.gpt_54_mini_model'), encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_54_mini_applicable_features', []);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_54_mini_image_generation_deployment', true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            Gpt54MiniFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Gpt54MiniFeature::deactivate();

            $this->migrator->deleteIfExists('ai.open_ai_gpt_54_mini_base_uri');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_54_mini_api_key');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_54_mini_model');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_54_mini_applicable_features');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_54_mini_model_name');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_54_mini_image_generation_deployment');
        });
    }
};
