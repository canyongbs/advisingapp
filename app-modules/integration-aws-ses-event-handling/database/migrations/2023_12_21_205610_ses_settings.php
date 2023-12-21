<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('ses', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('key');
            $blueprint->add('secret');
            $blueprint->add('region', 'us-east-1');
            $blueprint->add('configuration_set');
        });
    }
};
