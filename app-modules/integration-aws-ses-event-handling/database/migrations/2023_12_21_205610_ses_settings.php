<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('ses', function (SettingsBlueprint $blueprint): void {
            $blueprint->addEncrypted('configuration_set');
        });
    }
};
