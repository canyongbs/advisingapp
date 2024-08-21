<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('college_branding.is_enabled', false);
        $this->migrator->add('college_branding.college_text');
        $this->migrator->add('college_branding.color');
    }

    public function down(): void
    {
        $this->migrator->delete('college_branding.is_enabled');
        $this->migrator->delete('college_branding.college_text');
        $this->migrator->delete('college_branding.color');
    }
};
