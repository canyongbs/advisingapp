<?php

use Assist\Audit\Actions\Finders\AuditableModels;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('audit.retention_duration', 90);
        $this->migrator->add('audit.audited_models', AuditableModels::all()->keys());
    }
};
