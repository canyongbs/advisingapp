<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('ai-custom-advisor', function (SettingsBlueprint $blueprint) {
            try {
                $blueprint->add('allow_selection_of_model', true);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $blueprint->add('preselected_model', null);
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('ai-custom-advisor', function (SettingsBlueprint $blueprint) {
            $blueprint->delete('allow_selection_of_model');
            $blueprint->delete('preselected_model');
        });
    }
};
