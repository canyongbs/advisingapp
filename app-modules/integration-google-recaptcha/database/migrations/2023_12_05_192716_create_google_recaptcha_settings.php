<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('google-recaptcha', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('is_enabled', false);
            $blueprint->add('site_key');
            $blueprint->addEncrypted('secret_key');
        });
    }
};
