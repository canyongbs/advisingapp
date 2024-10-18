<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('manageable-student.is_enabled', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('manageable-student.is_enabled');
    }
};
