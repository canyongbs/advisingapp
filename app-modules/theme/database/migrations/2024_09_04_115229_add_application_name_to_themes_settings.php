<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('theme.application_name', 'Advising App');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('theme.application_name');
    }
};
