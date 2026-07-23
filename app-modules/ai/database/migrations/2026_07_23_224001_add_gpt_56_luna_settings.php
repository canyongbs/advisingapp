<?php

use App\Features\Gpt56LunaFeature;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            try {
                $this->migrator->add('ai.open_ai_gpt_56_luna_model_name');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_56_luna_base_uri', config('integration-open-ai.gpt_56_luna_base_uri'), encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_56_luna_api_key', config('integration-open-ai.gpt_56_luna_api_key'), encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_56_luna_model', config('integration-open-ai.gpt_56_luna_model'), encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_56_luna_applicable_features', []);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('ai.open_ai_gpt_56_luna_image_generation_deployment', encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            Gpt56LunaFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Gpt56LunaFeature::deactivate();

            $this->migrator->deleteIfExists('ai.open_ai_gpt_56_luna_base_uri');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_56_luna_api_key');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_56_luna_model');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_56_luna_applicable_features');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_56_luna_model_name');
            $this->migrator->deleteIfExists('ai.open_ai_gpt_56_luna_image_generation_deployment');
        });
    }
};
