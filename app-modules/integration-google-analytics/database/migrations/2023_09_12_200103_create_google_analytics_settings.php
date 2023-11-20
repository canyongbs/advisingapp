<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('google-analytics', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('is_enabled', false);
            $blueprint->add('id');
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('google-analytics', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('is_enabled');
            $blueprint->delete('id');
        });
    }
};
