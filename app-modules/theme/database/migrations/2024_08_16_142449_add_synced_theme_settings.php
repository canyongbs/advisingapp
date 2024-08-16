<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('theme.color_overrides', []);
        $this->migrator->add('theme.has_dark_mode', true);
        $this->migrator->add('theme.url');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('theme.color_overrides');
        $this->migrator->deleteIfExists('theme.has_dark_mode');
        $this->migrator->deleteIfExists('theme.url');
    }
};
