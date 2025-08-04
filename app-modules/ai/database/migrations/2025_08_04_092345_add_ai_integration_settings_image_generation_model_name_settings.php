<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        try {
            $this->migrator->add('ai.open_ai_gpt_4o_image_generation_model');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('ai.open_ai_gpt_4o_mini_image_generation_model');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('ai.open_ai_gpt_41_mini_image_generation_model');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('ai.open_ai_gpt_41_nano_image_generation_model');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('ai.open_ai_gpt_o3_image_generation_model');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4o_image_generation_model');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_4o_mini_image_generation_model');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_41_mini_image_generation_model');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_41_nano_image_generation_model');
        $this->migrator->deleteIfExists('ai.open_ai_gpt_o3_image_generation_model');
    }
};
