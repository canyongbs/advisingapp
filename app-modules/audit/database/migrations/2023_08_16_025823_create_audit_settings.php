<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('audit.retention_duration', 129600);
        $this->migrator->add('audit.audited_models', []);
    }
};
