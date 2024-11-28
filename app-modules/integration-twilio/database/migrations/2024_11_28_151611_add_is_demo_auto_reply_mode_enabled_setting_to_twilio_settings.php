<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('twilio', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('is_demo_auto_reply_mode_enabled', false);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('twilio', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('is_demo_auto_reply_mode_enabled');
        });
    }
};
