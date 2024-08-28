<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->deleteIfExists('audit.audited_models');

        try {
            $this->migrator->add('audit.audited_models_exclude', []);
        } catch (SettingAlreadyExists $exception) {
            // Ignore
        }
    }
};
