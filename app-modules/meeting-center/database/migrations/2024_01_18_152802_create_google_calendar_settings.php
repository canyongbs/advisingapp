<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('google_calendar', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('is_enabled', false);
            $blueprint->addEncrypted('client_id');
            $blueprint->addEncrypted('client_secret');
        });
    }
};
