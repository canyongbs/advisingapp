<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('student_information_system.is_enabled', false);
        $this->migrator->add('student_information_system.sis_system', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('student_information_system.is_enabled');
        $this->migrator->deleteIfExists('student_information_system.sis_system');
    }
};
