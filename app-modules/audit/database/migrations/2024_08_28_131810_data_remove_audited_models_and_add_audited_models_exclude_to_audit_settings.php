<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->delete('audit.audited_models');
        $this->migrator->add('audit.audited_models_exclude', []);
    }
};
