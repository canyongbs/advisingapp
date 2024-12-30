<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('azure_sso', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('matching_property', 'user_principal_name');
        });
    }
};
