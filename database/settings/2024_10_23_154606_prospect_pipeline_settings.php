<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('prospect_pipeline.is_enabled', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('prospect_pipeline.is_enabled');
    }
};
